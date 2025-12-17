<?php

namespace Tests\Feature;

use App\Models\BillingStatus;
use App\Models\MatrikTopEkspor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingPaymentRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_payment_due_date()
    {
        // Arrange
        $matrixTop = MatrikTopEkspor::create([
            'id_customer' => 'CUST001',
            'nama_customer' => 'PT Test Customer',
            'zterm' => 'Z001',
            'top' => 30, // 30 hari
            'produksi' => 'Y',
            'ekspor' => 'N',
        ]);

        $billing = BillingStatus::create([
            'weeks' => '202530',
            'vtweg' => '10',
            'bldat' => '2025-07-01',
            'tglgi' => '2025-07-01',
            'lfart' => 'ZLF',
            'mblnr' => 'INV001',
            'vbeln' => 'SO001',
            'vgbel' => 'DO001',
            'name1' => 'Test Customer',
            'name2' => '',
            'notes' => 'Test Notes',
            'fkdat' => '2025-07-01',
            'vbelm' => 'SO001',
            'stb' => 1000000,
            'stbk' => 1000000,
            'hna' => 900000,
            'tdisc' => 100000,
            'nett' => 900000,
            'lfstk' => 1,
            'vdatu' => '2025-07-01', // Tanggal dokumen
            'zterm' => 'Z001', // Relasi ke matrix
            'fkstk' => 1,
            'kunr' => 'CUST001',
        ]);

        // Act
        $billingWithRelation = BillingStatus::with('matrixTopEkspor')->find($billing->id);
        $paymentDueDate = $billingWithRelation->payment_due_date;

        // Assert
        $expectedDueDate = Carbon::parse('2025-07-01')->addDays(30)->format('Y-m-d');
        $this->assertEquals($expectedDueDate, $paymentDueDate);
        $this->assertEquals('2025-07-31', $paymentDueDate);
    }

    public function test_payment_due_date_returns_null_when_no_matrix_relation()
    {
        // Arrange
        $billing = BillingStatus::create([
            'weeks' => '202530',
            'vtweg' => '10',
            'bldat' => '2025-07-01',
            'tglgi' => '2025-07-01',
            'lfart' => 'ZLF',
            'mblnr' => 'INV002',
            'vbeln' => 'SO002',
            'vgbel' => 'DO002',
            'name1' => 'Test Customer 2',
            'name2' => '',
            'notes' => 'Test Notes',
            'fkdat' => '2025-07-01',
            'vbelm' => 'SO002',
            'stb' => 1000000,
            'stbk' => 1000000,
            'hna' => 900000,
            'tdisc' => 100000,
            'nett' => 900000,
            'lfstk' => 1,
            'vdatu' => '2025-07-01',
            'zterm' => 'Z999', // Tidak ada relasi
            'fkstk' => 1,
            'kunr' => 'CUST002',
        ]);

        // Act
        $billingWithRelation = BillingStatus::with('matrixTopEkspor')->find($billing->id);
        $paymentDueDate = $billingWithRelation->payment_due_date;

        // Assert
        $this->assertNull($paymentDueDate);
    }

    public function test_can_get_overdue_payments()
    {
        // Arrange
        $matrixTop = MatrikTopEkspor::create([
            'id_customer' => 'CUST003',
            'nama_customer' => 'PT Overdue Customer',
            'zterm' => 'Z002',
            'top' => 7, // 7 hari
            'produksi' => 'Y',
            'ekspor' => 'N',
        ]);

        $billing = BillingStatus::create([
            'weeks' => '202530',
            'vtweg' => '10',
            'bldat' => '2025-07-01',
            'tglgi' => '2025-07-01',
            'lfart' => 'ZLF',
            'mblnr' => 'INV003',
            'vbeln' => 'SO003',
            'vgbel' => 'DO003',
            'name1' => 'Overdue Customer',
            'name2' => '',
            'notes' => 'Test Notes',
            'fkdat' => '2025-07-01',
            'vbelm' => 'SO003',
            'stb' => 1000000,
            'stbk' => 1000000,
            'hna' => 900000,
            'tdisc' => 100000,
            'nett' => 900000,
            'lfstk' => 1,
            'vdatu' => now()->subDays(20)->format('Y-m-d'), // 20 hari lalu
            'zterm' => 'Z002',
            'fkstk' => 1,
            'kunr' => 'CUST003',
        ]);

        // Act
        $billingWithRelation = BillingStatus::with('matrixTopEkspor')->find($billing->id);
        $dueDate = $billingWithRelation->payment_due_date_carbon;

        // Assert
        $this->assertTrue($dueDate->isPast());
        $this->assertTrue($dueDate < now());
    }

    public function test_billing_controller_returns_correct_data()
    {
        // Arrange
        $matrixTop = MatrikTopEkspor::create([
            'id_customer' => 'CUST004',
            'nama_customer' => 'PT API Test Customer',
            'zterm' => 'Z003',
            'top' => 15, // 15 hari
            'produksi' => 'Y',
            'ekspor' => 'N',
        ]);

        $billing = BillingStatus::create([
            'weeks' => '202530',
            'vtweg' => '10',
            'bldat' => '2025-07-01',
            'tglgi' => '2025-07-01',
            'lfart' => 'ZLF',
            'mblnr' => 'INV004',
            'vbeln' => 'SO004',
            'vgbel' => 'DO004',
            'name1' => 'API Test Customer',
            'name2' => '',
            'notes' => 'Test Notes',
            'fkdat' => '2025-07-01',
            'vbelm' => 'SO004',
            'stb' => 1000000,
            'stbk' => 1000000,
            'hna' => 900000,
            'tdisc' => 100000,
            'nett' => 900000,
            'lfstk' => 1,
            'vdatu' => '2025-07-01',
            'zterm' => 'Z003',
            'fkstk' => 1,
            'kunr' => 'CUST004',
        ]);

        // Act
        $response = $this->get('/billing/' . $billing->id);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'vbeln',
            'name1',
            'vdatu',
            'zterm',
            'top_days',
            'payment_due_date',
            'customer_name',
            'nett',
            'matrix_data'
        ]);

        $data = $response->json();
        $this->assertEquals(15, $data['top_days']);
        $this->assertEquals('2025-07-16', $data['payment_due_date']);
    }
}
