# Dokumentasi Relasi Billing Status dan Matrix Top Ekspor

## Overview

Relasi ini digunakan untuk menghitung tanggal jatuh tempo pembayaran berdasarkan:

- `vdatu`: Tanggal dokumen dari tabel billing_statuses
- `top`: Nilai TOP (Terms of Payment) dalam hari dari tabel matrix_top_ekspor
- **Tanggal Jatuh Tempo = vdatu + top (hari)**

## Model BillingStatus

### Relasi yang Tersedia

1. **matrixTopEkspor()**: Relasi hasOne ke MatrikTopEkspor berdasarkan field `zterm`

### Accessor Attributes

1. **payment_due_date**: Mengembalikan tanggal jatuh tempo dalam format 'Y-m-d'
2. **payment_due_date_carbon**: Mengembalikan instance Carbon untuk tanggal jatuh tempo

### Scope

1. **withPaymentDueDate()**: Scope untuk eager loading relasi matrixTopEkspor

## Contoh Penggunaan

### 1. Mendapatkan Semua Billing dengan Tanggal Jatuh Tempo

```php
$billings = BillingStatus::withPaymentDueDate()->get();

foreach ($billings as $billing) {
    echo "Invoice: " . $billing->vbeln . "\n";
    echo "Tanggal Dokumen: " . $billing->vdatu . "\n";
    echo "TOP (Hari): " . ($billing->matrixTopEkspor?->top ?? 0) . "\n";
    echo "Tanggal Jatuh Tempo: " . $billing->payment_due_date . "\n";
    echo "Customer: " . ($billing->matrixTopEkspor?->nama_customer ?? 'N/A') . "\n";
    echo "---\n";
}
```

### 2. Mendapatkan Billing Tertentu

```php
$billing = BillingStatus::with('matrixTopEkspor')->find(1);

if ($billing) {
    $dueDate = $billing->payment_due_date_carbon;
    $isOverdue = $dueDate && $dueDate->isPast();

    echo "Invoice: " . $billing->vbeln . "\n";
    echo "Tanggal Jatuh Tempo: " . $billing->payment_due_date . "\n";
    echo "Status: " . ($isOverdue ? 'OVERDUE' : 'ACTIVE') . "\n";
}
```

### 3. Filter Pembayaran yang Jatuh Tempo

```php
$billings = BillingStatus::withPaymentDueDate()->get();

$overdue = $billings->filter(function ($billing) {
    $dueDate = $billing->payment_due_date_carbon;
    return $dueDate && $dueDate->isPast();
});

foreach ($overdue as $billing) {
    $daysOverdue = now()->diffInDays($billing->payment_due_date_carbon);
    echo "Invoice {$billing->vbeln} overdue by {$daysOverdue} days\n";
}
```

### 4. Mencari Pembayaran dalam Rentang Tanggal

```php
$startDate = now();
$endDate = now()->addDays(30);

$billings = BillingStatus::withPaymentDueDate()->get();

$paymentsDue = $billings->filter(function ($billing) use ($startDate, $endDate) {
    $dueDate = $billing->payment_due_date_carbon;
    if (!$dueDate) return false;

    return $dueDate->between($startDate, $endDate);
});
```

## API Endpoints

### 1. GET /billing

Mendapatkan semua billing dengan tanggal jatuh tempo

### 2. GET /billing/{id}

Mendapatkan billing tertentu dengan detail lengkap

### 3. GET /billing/overdue/payments

Mendapatkan semua pembayaran yang sudah jatuh tempo

### 4. GET /billing/due/payments

Mendapatkan pembayaran yang jatuh tempo dalam rentang tanggal tertentu

- Parameter: `start_date` (optional, default: hari ini)
- Parameter: `end_date` (optional, default: 30 hari ke depan)

## Format Response

```json
{
  "id": 1,
  "vbeln": "INV001",
  "name1": "Customer Name",
  "vdatu": "2025-07-01",
  "zterm": "Z001",
  "top_days": 30,
  "payment_due_date": "2025-07-31",
  "customer_name": "PT Customer ABC",
  "nett": 1000000
}
```

## Catatan Penting

1. Jika `vdatu` atau `top` tidak tersedia, `payment_due_date` akan mengembalikan `null`
2. Relasi menggunakan field `zterm` untuk menghubungkan kedua tabel
3. Tanggal jatuh tempo dihitung dengan menambahkan nilai `top` (dalam hari) ke `vdatu`
4. Gunakan `withPaymentDueDate()` scope untuk performa yang lebih baik saat memuat relasi
