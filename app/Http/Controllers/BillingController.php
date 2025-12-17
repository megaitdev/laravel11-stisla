<?php

namespace App\Http\Controllers;

use App\Models\BillingStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /** 
     * Get billing data with payment due date calculation
     */
    public function index($res = null)
    {
        $billings = BillingStatus::withPaymentDueDate()
            ->where(function ($query) {
                $query->where('ihrez', '!=', 'LUNAS')
                    ->orWhereNull('ihrez');
            })
            ->where(function ($query) {
                $query->where('augbl', '')
                    ->orWhereNull('augbl');
            })
            ->where('fkstk', 'C')
            ->where('vdatu', '!=', '0000-00-00')
            ->where('tglgi', '!=', '0000-00-00')
            // ->whereNotIn('vgbel', [
            //     '1050002729',
            //     '1050003183',
            //     '1050003247',
            //     '1050003286',
            //     '1050003392'
            // ])
            ->orderBy('vdatu', 'desc')
            ->get();

        // dd($billings);

        $result = $billings->map(function ($billing) {
            // Calculate due date from vdatu + zterm
            $dueDate = null;
            if ($billing->vdatu && $billing->zterm) {
                $vdatuDate = Carbon::createFromFormat('Y-m-d', $billing->vdatu);

                // Extract days from zterm (Y007 = 7 days, Y030 = 30 days)
                $termDays = 0;
                if (preg_match('/Y(\d+)/', $billing->zterm, $matches)) {
                    $termDays = (int)$matches[1];
                }

                $dueDate = $vdatuDate->addDays($termDays);
            }

            // Hitung selisih hari menggunakan tanggal saja (tanpa jam) untuk perbandingan yang akurat
            if ($dueDate) {
                $today = Carbon::today('Asia/Jakarta'); // Tanggal hari ini tanpa jam
                $dueDateOnly = $dueDate->copy()->startOfDay(); // Tanggal jatuh tempo tanpa jam

                // Hitung selisih hari: positif = belum jatuh tempo, negatif = sudah lewat jatuh tempo, 0 = hari ini
                $daysOverdue = round($today->diffInDays($dueDateOnly, false));
            } else {
                $daysOverdue = 0;
            }

            return [
                'id' => $billing->id,
                'surat_jalan' => $billing->vbeln,
                'so' => $billing->vgbel,
                'do' => $billing->mblnr,
                'customer_name' => $billing->name1,
                'vdatu' => $billing->vdatu, // Tanggal dokumen
                'erdav' => $billing->erdav, // Tanggal OC
                'erdai' => $billing->erdai, // Tanggal Billing
                'zterm' => $billing->zterm,
                'fkstk' => $billing->fkstk,
                'top_days' => $billing->matrixTopEkspor?->top ?? 0, // Hari TOP dari matrix
                'top' => $billing->payment_due_date, // Tanggal jatuh tempo pembayaran
                'top_carbon' => $billing->payment_due_date_carbon?->format('d/m/Y'),
                'days_payment' => $daysOverdue, // Hitung hari keterlamb
                'nett' => trim($billing->nett),
                'augbl' => $billing->augbl,
            ];
        });

        $filteredBillings = [
            // Before all days based on 'days_payment' (TOP)
            'all_before' => $result->filter(function ($billing) {
                return $billing['days_payment'] >= 0;
            })->values(),
            'top' => $result->filter(function ($billing) {
                return $billing['days_payment'] == 0;
            })->values(),
            'all_overdue' => $result->filter(function ($billing) {
                return $billing['days_payment'] < 0;
            })->values(),
        ];

        if ($res == null) {
            return response()->json([
                'result' => $result,
                'filtered_billings' => $filteredBillings,
            ]);
        }

        return $filteredBillings;
    }

    function notifySummaryOverdue()
    {
        $filteredBillings = $this->index('res');

        $message = "Selamat Siang Tim Ekspor,\n\n---\nPerihal: *Ringkasan Pembayaran Melewati Jatuh Tempo*\n\nBerikut adalah ringkasan pembayaran yang telah melewati jatuh tempo, per tanggal " . now()->format('d/m/Y') . ":\n\n";

        if (!empty($filteredBillings['overdue7'])) {
            $message .= "Jatuh Tempo +7 Hari: Terdapat *" . count($filteredBillings['overdue7']) . "* pembayaran \n";
        }
        if (!empty($filteredBillings['overdue14'])) {
            $message .= "Jatuh Tempo +14 Hari: Terdapat *" . count($filteredBillings['overdue14']) . "* pembayaran \n";
        }
        if (!empty($filteredBillings['overdue21'])) {
            $message .= "Jatuh Tempo +21 Hari: Terdapat *" . count($filteredBillings['overdue21']) . "* pembayaran \n";
        }
        if (!empty($filteredBillings['overdue30'])) {
            $message .= "Jatuh Tempo +30 Hari: Terdapat *" . count($filteredBillings['overdue30']) . "* pembayaran \n";
        }
        if (!empty($filteredBillings['overdue_more_than_30'])) {
            $message .= "Lebih dari 30 Hari: Terdapat *" . count($filteredBillings['overdue_more_than_30']) . "* pembayaran \n";
        }

        if (!empty($filteredBillings['all_overdue'])) {
            $message .= "\nTotal keseluruhan pembayaran yang melewati jatuh tempo adalah " . count($filteredBillings['all_overdue']) . " item.\n\n";
        }

        // $message .= "\nTerima kasih atas perhatian dan tindakan cepatnya.\n\nSalam,\nDirektur Utama PT.MAK\nDimas Prasetya";
        $message .= "\nTerima kasih atas perhatian dan tindakan cepatnya.\n\nSent FROM MEGACAN";

        // $nomor = '628989227992';
        // $nomor = '6285311901932';
        $users = [
            '628989227992', // Abima Nugraha
            // '6285311901932', // Helena Rengka
        ];
        foreach ($users as $nomor) {
            $this->megacanSendMessage($nomor, $message);
        }

        echo $message;
    }

    function getPesanNotifikasi(
        $namaUser,
        $dateTime,
        $upcomingBillings,
    ): string {
        $pesanNotifikasi = "🔔 Notifikasi Pembayaran Ekspor - PT MAK 🔔\n\n";
        $pesanNotifikasi .= "Yth. {$namaUser},\n\n";
        $pesanNotifikasi .= "------\n";
        $pesanNotifikasi .= "Tanggal: $dateTime WIB,\n";
        $pesanNotifikasi .= "Berikut adalah daftar pembayaran yang mendekati/sudah jatuh tempo:\n\n";
        $pesanNotifikasi .= "{$upcomingBillings}\n";
        $pesanNotifikasi .= "*Mohon respon pesan ini dengan jawaban (Ya/Tidak)*\n";
        $pesanNotifikasi .= "Terima kasih atas waktu dan perhatiannya.\n\nSalam,\nDirektur Utama PT. MAK\nDimas Prasetya";
        return $pesanNotifikasi;
    }


    function getPesanNotifikasioverdue(
        $namaUser,
        $dateTime,
        $overdueBillings,
    ): string {
        $pesanNotifikasi = "🔔 Notifikasi Pembayaran Ekspor - PT MAK 🔔\n\n";
        $pesanNotifikasi .= "Yth. {$namaUser},\n\n";
        $pesanNotifikasi .= "------\n";
        $pesanNotifikasi .= "Tanggal: $dateTime WIB,\n";
        $pesanNotifikasi .= "Berikut adalah daftar pembayaran sudah melewati jatuh tempo:\n\n";
        $pesanNotifikasi .= "{$overdueBillings}\n";
        $pesanNotifikasi .= "*Mohon respon pesan ini dengan jawaban (Ya/Tidak)*\n";
        $pesanNotifikasi .= "Terima kasih atas waktu dan perhatiannya.\n\nSalam,\nDirektur Utama PT. MAK\nDimas Prasetya";
        return $pesanNotifikasi;
    }



    function notifyUpcomingBilling($isMegadirut = false)
    {
        try {
            $upcomingBillings = $this->index('res')['all_before'];
            $upcomingBillings = collect($upcomingBillings)->filter(function ($item) {
                return $item['days_payment'] == 7 || $item['days_payment'] == 2;
            });

            if ($upcomingBillings->isEmpty()) {
                echo "Tidak ada pembayaran yang akan jatuh tempo dalam 7 atau 2 hari ke depan.\n";
                return; // Stop execution if no upcoming billings
            }

            Carbon::setLocale('id'); // Set locale to Indonesian for translated dates
            $currentDateTime = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i');

            $upcomingMessage = "";
            foreach ($upcomingBillings as $item) {
                // Format tanggal agar lebih mudah dibaca, misal "05 Agustus 2025"
                $formattedTop = \Carbon\Carbon::parse($item['top'])->translatedFormat('d F Y');
                $upcomingMessage .= "🚩 OC: " . $item['so'] . " (Customer: " . $item['customer_name'] . ", Jatuh Tempo: " . $formattedTop . ")\n";
            }

            $usersMegadirut = [
                [
                    'nama' => 'Bpk Dimas Prasetya',
                    'nomor' => '628121056987',
                ],
                [
                    'nama' => 'Ibu Marta Tjiptaningsih',
                    'nomor' => '6289604624356',
                ],
                [
                    'nama' => 'Ibu Diah Widowati',
                    'nomor' => '6281932123619',
                ],
                [
                    'nama' => 'Ibu E. Dwi Etnawati',
                    'nomor' => '6282123736460',
                ],
                [
                    'nama' => 'Ibu Titin Jaelani',
                    'nomor' => '6285716089853',
                ],
                [
                    'nama' => 'Bpk Eko',
                    'nomor' => '628119998772',
                ],
            ];

            $usersMegacan = [
                [
                    'nama' => 'Ibu Titin Jaelani',
                    'nomor' => '6285716089853',
                ],
                [
                    'nama' => 'Bpk Eko',
                    'nomor' => '628119998772',
                ],
            ];


            if ($isMegadirut) {
                foreach ($usersMegadirut as $key => $user) {
                    $message = $this->getPesanNotifikasi($user['nama'], $currentDateTime, $upcomingMessage);
                    $this->megadirutSendMessage($user['nomor'], $message);
                }
            } else {
                foreach ($usersMegacan as $key => $user) {
                    $message = $this->getPesanNotifikasi($user['nama'], $currentDateTime, $upcomingMessage);
                    $this->megacanSendMessage($user['nomor'], $message);
                }
            }
            $message = $this->getPesanNotifikasi('Abima Nugraha', $currentDateTime, $upcomingMessage);
            $this->megacanSendMessage('628989227992', $message);

            echo $message;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in notifyUpcomingBilling: ' . $e->getMessage());
            echo "An error occurred: " . $e->getMessage();
        }
    }
    function notifyOverdueBilling($isMegadirut = false)
    {
        try {
            $overdueBillings = $this->index('res')['all_overdue'];

            if ($overdueBillings->isEmpty()) {
                echo "Tidak ada pembayaran yang melewati jatuh tempo.\n";
                return; // Stop execution if no overdue billings
            }

            Carbon::setLocale('id'); // Set locale to Indonesian for translated dates
            $currentDateTime = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i');

            $overdueMessage = "";
            foreach ($overdueBillings as $item) {
                // Format tanggal agar lebih mudah dibaca, misal "05 Agustus 2025"
                $formattedTop = \Carbon\Carbon::parse($item['top'])->translatedFormat('d F Y');
                $overdueMessage .= "🚩 OC: " . $item['so'] . " (Customer: " . $item['customer_name'] . ", Jatuh Tempo: " . $formattedTop . ")\n";
            }

            $usersMegadirut = [
                [
                    'nama' => 'Bpk Dimas Prasetya',
                    'nomor' => '628121056987',
                ],
                [
                    'nama' => 'Ibu Marta Tjiptaningsih',
                    'nomor' => '6289604624356',
                ],
                [
                    'nama' => 'Ibu Diah Widowati',
                    'nomor' => '6281932123619',
                ],
                [
                    'nama' => 'Ibu E. Dwi Etnawati',
                    'nomor' => '6282123736460',
                ],
                [
                    'nama' => 'Ibu Titin Jaelani',
                    'nomor' => '6285716089853',
                ],
                [
                    'nama' => 'Bpk Eko',
                    'nomor' => '628119998772',
                ],
            ];

            $usersMegacan = [
                [
                    'nama' => 'Ibu Titin Jaelani',
                    'nomor' => '6285716089853',
                ],
                [
                    'nama' => 'Bpk Eko',
                    'nomor' => '628119998772',
                ],
            ];

            if ($isMegadirut) {
                foreach ($usersMegadirut as $key => $user) {
                    $message = $this->getPesanNotifikasioverdue($user['nama'], $currentDateTime, $overdueMessage);
                    $this->megadirutSendMessage($user['nomor'], $message);
                }
            } else {
                foreach ($usersMegacan as $key => $user) {
                    $message = $this->getPesanNotifikasioverdue($user['nama'], $currentDateTime, $overdueMessage);
                    $this->megacanSendMessage($user['nomor'], $message);
                }
            }

            $message = $this->getPesanNotifikasioverdue('Abima Nugraha', $currentDateTime, $overdueMessage);
            $this->megacanSendMessage('628989227992', $message);
            echo $message;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in notifyOverdueBilling: ' . $e->getMessage());
            echo "An error occurred: " . $e->getMessage();
        }
    }

    function getPesanDaftarOCBesok(): string
    {
        // Ambil semua billing yang belum jatuh tempo
        $upcomingBillings = $this->index('res')['all_before'];

        // Filter billing yang hari ini sisa 8 atau 3 hari (besok akan menjadi 7 atau 2 dan akan dinotifikasi)
        $tomorrowBillings = collect($upcomingBillings)->filter(function ($item) {
            return $item['days_payment'] == 8 || $item['days_payment'] == 3 || $item['days_payment'] == 1;
        });

        if ($tomorrowBillings->isEmpty()) {
            return "Tidak ada OC incoming yang akan dinotifikasi besok.\n";
        }

        Carbon::setLocale('id');
        $currentDateTime = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i');
        $tomorrow = Carbon::tomorrow('Asia/Jakarta');
        $tomorrowDate = $tomorrow->translatedFormat('l, d F Y'); // Contoh: Selasa, 22 Oktober 2025

        // Urutkan dari yang paling dekat jatuh tempo (days_payment paling kecil)
        $sortedBillings = $tomorrowBillings->sortBy('days_payment');

        $message = "📋 Daftar OC yang Akan Dinotifikasi Besok\n\n";
        $message .= "Tanggal: {$currentDateTime} WIB\n";
        $message .= "Notifikasi akan dikirim: {$tomorrowDate}\n";
        $message .= "Total: " . $sortedBillings->count() . " OC\n\n";
        $message .= "---\n\n";
        $message .= "📊 Diurutkan dari yang paling dekat jatuh tempo:\n\n";

        $no = 1;
        foreach ($sortedBillings as $item) {
            $formattedTop = Carbon::parse($item['top'])->translatedFormat('d F Y');
            $sisaHariHariIni = $item['days_payment'];
            $sisaHariBesok = $sisaHariHariIni - 1; // Besok akan berkurang 1 hari

            // Tentukan emoji dan label H berdasarkan sisa hari
            $emoji = '🔔';
            $hLabel = '';
            if ($sisaHariBesok == 0) {
                $emoji = '🔴'; // H-0
                $hLabel = ' (H-0)';
            } elseif ($sisaHariBesok == 2) {
                $emoji = '⚠️'; // H-2
                $hLabel = ' (H-2)';
            } elseif ($sisaHariBesok == 7) {
                $emoji = '📢'; // H-7
                $hLabel = ' (H-7)';
            }

            $message .= "{$emoji} {$no}. OC: {$item['so']}{$hLabel}\n";
            $message .= "   Customer: {$item['customer_name']}\n";
            $message .= "   Jatuh Tempo: {$formattedTop}\n";
            $message .= "   Sisa Hari Besok: {$sisaHariBesok} hari (akan dinotifikasi)\n\n";

            $no++;
        }

        $message .= "---\n";
        $message .= "Notifikasi akan dikirim otomatis besok kepada:\n";
        $message .= "- Bpk Dimas Prasetya\n";
        $message .= "- Ibu Diah Widowati\n";
        $message .= "- Ibu E. Dwi Etnawati\n";
        $message .= "- Ibu Marta Tjiptaningsih\n";
        $message .= "- Ibu Titin Jaelani\n";
        $message .= "- Bpk Eko\n\n";
        $message .= "📅 Jadwal notifikasi upcoming: Setiap hari (H-7, H-2, dan H-0)\n\n";
        $message .= "---\n";
        $message .= "📄 Untuk melihat detail lengkap, silakan akses:\n";
        $message .= "https://app.mak-techno.co.id/ekspor/list_oc.php?status=incoming&notification=tomorrow\n\n";
        $message .= "Sent FROM MEGACAN";

        return $message;
    }

    function notifyOCBesok()
    {
        try {
            $message = $this->getPesanDaftarOCBesok();

            // Kirim ke PIC yang ditentukan
            $users = [
                [
                    'nama' => 'Abima Nugraha',
                    'nomor' => '628989227992',
                ],
                [
                    'nama' => 'Ibu Titin Jaelani',
                    'nomor' => '6285716089853',
                ],
                [
                    'nama' => 'Bpk Eko',
                    'nomor' => '628119998772',
                ],
            ];

            foreach ($users as $user) {
                $this->megacanSendMessage($user['nomor'], $message);
            }

            echo $message;

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi OC besok berhasil dikirim',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in notifyOCBesok: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    function getPesanDaftarOCOverdueBesok(): string
    {
        // Ambil semua billing yang sudah overdue
        $overdueBillings = $this->index('res')['all_overdue'];

        if ($overdueBillings->isEmpty()) {
            return "Tidak ada pembayaran overdue yang akan dinotifikasi besok.\n";
        }

        // Cek apakah besok adalah Senin, Rabu, atau Jumat
        $tomorrow = Carbon::tomorrow('Asia/Jakarta');
        $dayOfWeek = $tomorrow->dayOfWeek; // 0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu

        $isNotificationDay = in_array($dayOfWeek, [1, 3, 5]); // 1=Senin, 3=Rabu, 5=Jumat

        if (!$isNotificationDay) {
            $nextDay = $tomorrow->translatedFormat('l'); // Nama hari dalam bahasa Indonesia
            return "Besok adalah hari {$nextDay}. Notifikasi overdue hanya dikirim pada hari Senin, Rabu, dan Jumat.\n";
        }

        // Jika tidak ada data overdue, beri informasi bahwa tidak ada notifikasi yang akan dikirim
        if ($overdueBillings->isEmpty()) {
            return "Tidak ada OC overdue. Tidak akan ada notifikasi overdue yang dikirim besok ({$tomorrow->translatedFormat('l, d F Y')}).\n";
        }

        Carbon::setLocale('id');
        $currentDateTime = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i');
        $tomorrowDate = $tomorrow->translatedFormat('l, d F Y'); // Contoh: Senin, 22 Oktober 2025

        // Urutkan dari yang paling terlambat (days_payment paling kecil/negatif terbesar)
        $sortedOverdue = $overdueBillings->sortBy('days_payment');

        $message = "📋 Daftar OC Overdue yang Akan Dinotifikasi Besok\n\n";
        $message .= "Tanggal: {$currentDateTime} WIB\n";
        $message .= "Notifikasi akan dikirim: {$tomorrowDate}\n";
        $message .= "Total: " . $sortedOverdue->count() . " OC Overdue\n\n";
        $message .= "---\n\n";
        $message .= "📊 Diurutkan dari overdue terlama:\n\n";

        $no = 1;
        foreach ($sortedOverdue as $item) {
            $formattedTop = Carbon::parse($item['top'])->translatedFormat('d F Y');
            $daysLate = abs($item['days_payment']);

            // Tentukan emoji berdasarkan tingkat keterlambatan
            $emoji = '⚠️';
            if ($daysLate > 30) {
                $emoji = '⛔';
            } elseif ($daysLate > 21) {
                $emoji = '🚨';
            } elseif ($daysLate > 14) {
                $emoji = '🔴';
            } elseif ($daysLate > 7) {
                $emoji = '🔶';
            }

            $message .= "{$emoji} {$no}. OC: {$item['so']}\n";
            $message .= "   Customer: {$item['customer_name']}\n";
            $message .= "   Jatuh Tempo: {$formattedTop}\n";
            $message .= "   Terlambat: {$daysLate} hari\n\n";

            $no++;
        }

        $message .= "---\n";
        $message .= "Notifikasi akan dikirim otomatis besok kepada:\n";
        $message .= "- Bpk Dimas Prasetya\n";
        $message .= "- Ibu Diah Widowati\n";
        $message .= "- Ibu E. Dwi Etnawati\n";
        $message .= "- Ibu Marta Tjiptaningsih\n";
        $message .= "- Ibu Titin Jaelani\n";
        $message .= "- Bpk Eko\n\n";

        $message .= "📅 Jadwal notifikasi overdue: Setiap Senin, Rabu, dan Jumat\n\n";
        $message .= "---\n";
        $message .= "📄 Untuk melihat detail lengkap, silakan akses:\n";
        $message .= "https://app.mak-techno.co.id/ekspor/list_oc.php?status=overdue&notification=tomorrow\n\n";
        $message .= "Sent FROM MEGACAN";

        return $message;
    }

    function notifyOCOverdueBesok()
    {
        try {
            $message = $this->getPesanDaftarOCOverdueBesok();

            // Kirim ke PIC yang ditentukan
            $users = [
                [
                    'nama' => 'Abima Nugraha',
                    'nomor' => '628989227992',
                ],
                [
                    'nama' => 'Ibu Titin Jaelani',
                    'nomor' => '6285716089853',
                ],
                [
                    'nama' => 'Bpk Eko',
                    'nomor' => '628119998772',
                ],
            ];

            foreach ($users as $user) {
                $this->megacanSendMessage($user['nomor'], $message);
            }

            echo $message;

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi OC Overdue besok berhasil dikirim',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in notifyOCOverdueBesok: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
