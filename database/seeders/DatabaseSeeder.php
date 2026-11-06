<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Customer;
use App\Models\DeliveryDocument;
use App\Models\DeliveryLocation;
use App\Models\DeliveryProof;
use App\Models\Driver;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\Rating;
use App\Models\TariffSetting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tariff Settings
        $tariff = TariffSetting::create([
            'water_price_per_liter' => 50.00, // Rp 50/L
            'delivery_price_per_km' => 15000.00, // Rp 15.000/km
            'base_fee' => 50000.00, // Rp 50.000
            'company_name' => 'Tirta Mandiri Water Logistics',
            'company_address' => 'Jl. Pengisian Armada Tangki No. 10, Gambir, Jakarta Pusat',
            'company_phone' => '+62 811-9876-5432',
            'company_email' => 'operasional@tirtamandiri.co.id',
            'depot_latitude' => -6.2088000,
            'depot_longitude' => 106.8456000,
        ]);

        // 2. Admin User
        $admin = User::create([
            'name' => 'Administrator Tirta Mandiri',
            'email' => 'admin@tirtamandiri.co.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081198765432',
            'status' => 'active',
        ]);

        // 3. Vehicles (Armada Truk Tangki)
        $v1 = Vehicle::create([
            'plate_number' => 'B 9101 TTA',
            'capacity' => 7000,
            'vehicle_type' => 'Tangki 7.000 L',
            'brand_model' => 'Isuzu Elf NMR 71 HD',
            'status' => 'Ready',
            'current_latitude' => -6.2088000,
            'current_longitude' => 106.8456000,
            'notes' => 'Kondisi prima, pompa hisap baru diservis.',
        ]);

        $v2 = Vehicle::create([
            'plate_number' => 'B 9102 TTB',
            'capacity' => 8000,
            'vehicle_type' => 'Tangki 8.000 L',
            'brand_model' => 'Hino Dutro 130 HD Tanker',
            'status' => 'On Duty',
            'current_latitude' => -6.1850000,
            'current_longitude' => 106.8750000,
            'notes' => 'Sedang dalam pengiriman menuju Pulogadung.',
        ]);

        $v3 = Vehicle::create([
            'plate_number' => 'B 9201 TTC',
            'capacity' => 16000,
            'vehicle_type' => 'Tangki 16.000 L',
            'brand_model' => 'Mitsubishi Fuso Fighter FN 61',
            'status' => 'Ready',
            'current_latitude' => -6.2088000,
            'current_longitude' => 106.8456000,
            'notes' => 'Tangki stainless food-grade siap operasi.',
        ]);

        $v4 = Vehicle::create([
            'plate_number' => 'B 9202 TTD',
            'capacity' => 20000,
            'vehicle_type' => 'Tangki 20.000 L',
            'brand_model' => 'Hino Ranger FM 260 JD Tanker',
            'status' => 'Ready',
            'current_latitude' => -6.2088000,
            'current_longitude' => 106.8456000,
            'notes' => 'Kapasitas besar untuk proyek konstruksi dan industri.',
        ]);

        $v5 = Vehicle::create([
            'plate_number' => 'B 9301 TTE',
            'capacity' => 8000,
            'vehicle_type' => 'Tangki 8.000 L',
            'brand_model' => 'Mitsubishi Fuso Canter 125 PS',
            'status' => 'Maintenance',
            'current_latitude' => -6.2150000,
            'current_longitude' => 106.8500000,
            'notes' => 'Sedang penggantian kampas rem dan seal pompa.',
        ]);

        // 4. Drivers
        $uDriver1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.driver@tirtamandiri.co.id',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'phone' => '08123456701',
            'status' => 'active',
        ]);
        $driver1 = Driver::create([
            'user_id' => $uDriver1->id,
            'license_number' => 'SIM-B2-8829102',
            'identity_number' => '3171012304850001',
            'availability_status' => 'Aktif',
            'verification_status' => 'verified',
            'current_latitude' => -6.2088000,
            'current_longitude' => 106.8456000,
            'last_location_update' => now(),
        ]);

        $uDriver2 = User::create([
            'name' => 'Agus Prasetyo',
            'email' => 'agus.driver@tirtamandiri.co.id',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'phone' => '08123456702',
            'status' => 'active',
        ]);
        $driver2 = Driver::create([
            'user_id' => $uDriver2->id,
            'license_number' => 'SIM-B2-8829103',
            'identity_number' => '3171012405870002',
            'availability_status' => 'On Delivery',
            'verification_status' => 'verified',
            'current_latitude' => -6.1850000,
            'current_longitude' => 106.8750000,
            'last_location_update' => now(),
        ]);

        $uDriver3 = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat.driver@tirtamandiri.co.id',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'phone' => '08123456703',
            'status' => 'active',
        ]);
        $driver3 = Driver::create([
            'user_id' => $uDriver3->id,
            'license_number' => 'SIM-B2-8829104',
            'identity_number' => '3171011508890003',
            'availability_status' => 'Aktif',
            'verification_status' => 'verified',
            'current_latitude' => -6.2088000,
            'current_longitude' => 106.8456000,
            'last_location_update' => now(),
        ]);

        $uDriver4 = User::create([
            'name' => 'Deden Kurnia (Baru)',
            'email' => 'deden.driver@tirtamandiri.co.id',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'phone' => '08123456704',
            'status' => 'pending',
        ]);
        $driver4 = Driver::create([
            'user_id' => $uDriver4->id,
            'license_number' => 'SIM-B2-8829105',
            'identity_number' => '3171011909920004',
            'availability_status' => 'Libur',
            'verification_status' => 'pending',
        ]);

        // 5. Customers
        $uCust1 = User::create([
            'name' => 'Hendri Kurniawan',
            'email' => 'hendri@hotelsantika.co.id',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '08198765431',
            'status' => 'active',
        ]);
        $cust1 = Customer::create([
            'user_id' => $uCust1->id,
            'company_name' => 'Hotel Santika Premiere Slipi',
            'contact_person' => 'Hendri Kurniawan (Chief Engineer)',
            'address' => 'Jl. K.S. Tubun No. 7, Slipi, Palmerah, Jakarta Barat',
            'latitude' => -6.1920000,
            'longitude' => 106.7980000,
            'business_category' => 'Hotel',
        ]);

        $uCust2 = User::create([
            'name' => 'Ir. Dedi Sulaeman',
            'email' => 'dedi@wijayakonstruksi.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '08198765432',
            'status' => 'active',
        ]);
        $cust2 = Customer::create([
            'user_id' => $uCust2->id,
            'company_name' => 'PT Wijaya Konstruksi Megah',
            'contact_person' => 'Ir. Dedi Sulaeman (Site Project Manager)',
            'address' => 'Proyek Gedung Tower B, Jl. TB Simatupang Kav. 88, Cilandak, Jakarta Selatan',
            'latitude' => -6.2940000,
            'longitude' => 106.8050000,
            'business_category' => 'Proyek Konstruksi',
        ]);

        $uCust3 = User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'siti@indofoodms.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '08198765433',
            'status' => 'active',
        ]);
        $cust3 = Customer::create([
            'user_id' => $uCust3->id,
            'company_name' => 'PT Tirta Beverage Nusantara',
            'contact_person' => 'Siti Rahmawati (Procurement)',
            'address' => 'Kawasan Industri Pulogadung Blok B No. 12, Jakarta Timur',
            'latitude' => -6.1950000,
            'longitude' => 106.9100000,
            'business_category' => 'Pabrik/Industri',
        ]);

        // 6. Orders in various workflows

        // Order 1: Pending (Customer 1, 5.000 L -> Tangki 7.000 L)
        $dist1 = Order::calculateHaversineDistance($tariff->depot_latitude, $tariff->depot_longitude, $cust1->latitude, $cust1->longitude);
        $wPrice1 = 5000 * $tariff->water_price_per_liter;
        $dPrice1 = $tariff->base_fee + ($dist1 * $tariff->delivery_price_per_km);
        $ord1 = Order::create([
            'order_number' => 'ORD-20261106-0001',
            'customer_id' => $cust1->id,
            'volume' => 5000,
            'fleet_category_recommendation' => 'Tangki 7.000 L',
            'trips_count' => 1,
            'delivery_address' => $cust1->address,
            'latitude' => $cust1->latitude,
            'longitude' => $cust1->longitude,
            'delivery_date' => Carbon::today(),
            'recipient_name' => 'Pak Joko (Security Loading Dock)',
            'recipient_phone' => '081299887711',
            'notes' => 'Akses melalui pintu gerbang loading dock belakang hotel.',
            'estimated_distance' => $dist1,
            'water_price_rate' => $tariff->water_price_per_liter,
            'delivery_price_rate' => $tariff->delivery_price_per_km,
            'water_price' => $wPrice1,
            'delivery_price' => $dPrice1,
            'total_price' => $wPrice1 + $dPrice1,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
        ]);

        // Order 2: Diproses (Customer 2, 10.000 L -> Tangki 16.000 L, assigned to Driver 1 & Vehicle 3)
        $dist2 = Order::calculateHaversineDistance($tariff->depot_latitude, $tariff->depot_longitude, $cust2->latitude, $cust2->longitude);
        $wPrice2 = 10000 * $tariff->water_price_per_liter;
        $dPrice2 = $tariff->base_fee + ($dist2 * $tariff->delivery_price_per_km);
        $ord2 = Order::create([
            'order_number' => 'ORD-20261106-0002',
            'customer_id' => $cust2->id,
            'volume' => 10000,
            'fleet_category_recommendation' => 'Tangki 16.000 L',
            'trips_count' => 1,
            'delivery_address' => $cust2->address,
            'latitude' => $cust2->latitude,
            'longitude' => $cust2->longitude,
            'delivery_date' => Carbon::today(),
            'recipient_name' => 'Mandor Proyek Bambang',
            'recipient_phone' => '081388776655',
            'notes' => 'Pengisian ke ground tank proyek tower B.',
            'estimated_distance' => $dist2,
            'water_price_rate' => $tariff->water_price_per_liter,
            'delivery_price_rate' => $tariff->delivery_price_per_km,
            'water_price' => $wPrice2,
            'delivery_price' => $dPrice2,
            'total_price' => $wPrice2 + $dPrice2,
            'status' => 'Diproses',
            'payment_status' => 'Down Payment',
        ]);

        $assign2 = Assignment::create([
            'order_id' => $ord2->id,
            'driver_id' => $driver1->id,
            'vehicle_id' => $v3->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now()->subHours(2),
            'accepted_at' => now()->subHours(1)->addMinutes(30),
            'status' => 'Accepted',
        ]);

        $sjjNumber2 = 'SJJ/' . date('Y/m') . '/0001';
        DeliveryDocument::create([
            'order_id' => $ord2->id,
            'assignment_id' => $assign2->id,
            'document_number' => $sjjNumber2,
            'revision' => 1,
            'issued_at' => now()->subHours(2),
            'issued_by' => $admin->id,
        ]);

        // Order 3: Dikirim (Customer 3, 8.000 L -> Tangki 8.000 L, assigned to Driver 2 & Vehicle 2)
        $dist3 = Order::calculateHaversineDistance($tariff->depot_latitude, $tariff->depot_longitude, $cust3->latitude, $cust3->longitude);
        $wPrice3 = 8000 * $tariff->water_price_per_liter;
        $dPrice3 = $tariff->base_fee + ($dist3 * $tariff->delivery_price_per_km);
        $ord3 = Order::create([
            'order_number' => 'ORD-20261106-0003',
            'customer_id' => $cust3->id,
            'volume' => 8000,
            'fleet_category_recommendation' => 'Tangki 8.000 L',
            'trips_count' => 1,
            'delivery_address' => $cust3->address,
            'latitude' => $cust3->latitude,
            'longitude' => $cust3->longitude,
            'delivery_date' => Carbon::today(),
            'recipient_name' => 'Operator Bak Penampungan Bu Rina',
            'recipient_phone' => '081577665544',
            'notes' => 'Harap konfirmasi sebelum masuk pos keamanan kawasan industri.',
            'estimated_distance' => $dist3,
            'water_price_rate' => $tariff->water_price_per_liter,
            'delivery_price_rate' => $tariff->delivery_price_per_km,
            'water_price' => $wPrice3,
            'delivery_price' => $dPrice3,
            'total_price' => $wPrice3 + $dPrice3,
            'status' => 'Dikirim',
            'payment_status' => 'Paid',
        ]);

        $assign3 = Assignment::create([
            'order_id' => $ord3->id,
            'driver_id' => $driver2->id,
            'vehicle_id' => $v2->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now()->subHours(3),
            'accepted_at' => now()->subHours(2)->addMinutes(45),
            'started_at' => now()->subHour(),
            'status' => 'In Transit',
        ]);

        $sjjNumber3 = 'SJJ/' . date('Y/m') . '/0002';
        DeliveryDocument::create([
            'order_id' => $ord3->id,
            'assignment_id' => $assign3->id,
            'document_number' => $sjjNumber3,
            'revision' => 1,
            'issued_at' => now()->subHours(3),
            'issued_by' => $admin->id,
        ]);

        // GPS Tracking breadcrumbs for Order 3
        DeliveryLocation::create([
            'assignment_id' => $assign3->id,
            'driver_id' => $driver2->id,
            'latitude' => -6.2088000,
            'longitude' => 106.8456000,
            'speed' => 0,
            'heading' => 45,
            'recorded_at' => now()->subMinutes(50),
        ]);
        DeliveryLocation::create([
            'assignment_id' => $assign3->id,
            'driver_id' => $driver2->id,
            'latitude' => -6.1980000,
            'longitude' => 106.8620000,
            'speed' => 38.5,
            'heading' => 60,
            'recorded_at' => now()->subMinutes(25),
        ]);
        DeliveryLocation::create([
            'assignment_id' => $assign3->id,
            'driver_id' => $driver2->id,
            'latitude' => -6.1850000,
            'longitude' => 106.8750000,
            'speed' => 25.0,
            'heading' => 70,
            'recorded_at' => now()->subMinutes(5),
        ]);

        // Order 4: Selesai (Customer 1, 16.000 L -> Tangki 16.000 L, Completed with Proof, Financial Transaction, and Rating)
        $dist4 = Order::calculateHaversineDistance($tariff->depot_latitude, $tariff->depot_longitude, $cust1->latitude, $cust1->longitude);
        $wPrice4 = 16000 * $tariff->water_price_per_liter;
        $dPrice4 = $tariff->base_fee + ($dist4 * $tariff->delivery_price_per_km);
        $total4 = $wPrice4 + $dPrice4;
        $ord4 = Order::create([
            'order_number' => 'ORD-20261105-0001',
            'customer_id' => $cust1->id,
            'volume' => 16000,
            'fleet_category_recommendation' => 'Tangki 16.000 L',
            'trips_count' => 1,
            'delivery_address' => $cust1->address,
            'latitude' => $cust1->latitude,
            'longitude' => $cust1->longitude,
            'delivery_date' => Carbon::yesterday(),
            'recipient_name' => 'Hendri Kurniawan',
            'recipient_phone' => '08198765431',
            'notes' => 'Pengisian reservoir cadangan air bersih hotel.',
            'estimated_distance' => $dist4,
            'water_price_rate' => $tariff->water_price_per_liter,
            'delivery_price_rate' => $tariff->delivery_price_per_km,
            'water_price' => $wPrice4,
            'delivery_price' => $dPrice4,
            'total_price' => $total4,
            'status' => 'Selesai',
            'payment_status' => 'Paid',
        ]);

        $assign4 = Assignment::create([
            'order_id' => $ord4->id,
            'driver_id' => $driver1->id,
            'vehicle_id' => $v3->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now()->subDay()->subHours(6),
            'accepted_at' => now()->subDay()->subHours(5),
            'started_at' => now()->subDay()->subHours(4),
            'arrived_at' => now()->subDay()->subHours(3),
            'completed_at' => now()->subDay()->subHours(2),
            'status' => 'Completed',
        ]);

        $sjjNumber4 = 'SJJ/' . date('Y/m') . '/0000';
        DeliveryDocument::create([
            'order_id' => $ord4->id,
            'assignment_id' => $assign4->id,
            'document_number' => $sjjNumber4,
            'revision' => 1,
            'issued_at' => now()->subDay()->subHours(6),
            'issued_by' => $admin->id,
        ]);

        DeliveryProof::create([
            'order_id' => $ord4->id,
            'assignment_id' => $assign4->id,
            'driver_id' => $driver1->id,
            'photo_path' => 'proofs/sample_proof.jpg',
            'recipient_name' => 'Hendri Kurniawan',
            'notes' => 'Pengisian 16.000 L air bersih selesai tanpa kendala. Segel tangki diperiksa bersama.',
            'completed_at' => now()->subDay()->subHours(2),
        ]);

        FinancialTransaction::create([
            'order_id' => $ord4->id,
            'type' => 'income',
            'category' => 'Penjualan Air',
            'amount' => $total4,
            'transaction_date' => Carbon::yesterday(),
            'description' => "Pemasukan otomatis dari pesanan selesai {$ord4->order_number} (16.000 L ke {$cust1->company_name})",
            'created_by' => $admin->id,
        ]);

        Rating::create([
            'order_id' => $ord4->id,
            'customer_id' => $cust1->id,
            'driver_id' => $driver1->id,
            'rating' => 5,
            'review' => 'Pelayanan sangat cepat, driver ramah dan profesional, air sangat jernih dan berkualitas.',
        ]);

        // Order 5: Pending Review / Kendala (Customer 2, Driver 3)
        $dist5 = Order::calculateHaversineDistance($tariff->depot_latitude, $tariff->depot_longitude, $cust2->latitude, $cust2->longitude);
        $wPrice5 = 18000 * $tariff->water_price_per_liter;
        $dPrice5 = $tariff->base_fee + ($dist5 * $tariff->delivery_price_per_km);
        $ord5 = Order::create([
            'order_number' => 'ORD-20261106-0004',
            'customer_id' => $cust2->id,
            'volume' => 18000,
            'fleet_category_recommendation' => 'Tangki 20.000 L',
            'trips_count' => 1,
            'delivery_address' => $cust2->address,
            'latitude' => $cust2->latitude,
            'longitude' => $cust2->longitude,
            'delivery_date' => Carbon::today(),
            'recipient_name' => 'Pak Dedi',
            'recipient_phone' => '08198765432',
            'notes' => 'Pengiriman mendesak.',
            'estimated_distance' => $dist5,
            'water_price_rate' => $tariff->water_price_per_liter,
            'delivery_price_rate' => $tariff->delivery_price_per_km,
            'water_price' => $wPrice5,
            'delivery_price' => $dPrice5,
            'total_price' => $wPrice5 + $dPrice5,
            'status' => 'Pending Review',
            'payment_status' => 'Unpaid',
            'pending_reason' => 'Driver melaporkan kendala di jalan: ban kiri belakang bocor saat perjalanan.',
        ]);

        $assign5 = Assignment::create([
            'order_id' => $ord5->id,
            'driver_id' => $driver3->id,
            'vehicle_id' => $v4->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now()->subHours(2),
            'accepted_at' => now()->subHours(1)->addMinutes(45),
            'started_at' => now()->subHour(),
            'status' => 'Cancelled',
            'driver_issue_reason' => 'Ban kiri belakang armada bocor terkena paku di bypass tol Cawang. Menunggu bantuan tambal ban mobile.',
        ]);

        // 7. Manual Expenses
        FinancialTransaction::create([
            'type' => 'expense',
            'category' => 'Bensin/Solar',
            'amount' => 450000.00,
            'transaction_date' => Carbon::today(),
            'description' => 'Pengisian Solar Dexlite 35 Liter armada B 9102 TTB',
            'created_by' => $admin->id,
        ]);

        FinancialTransaction::create([
            'type' => 'expense',
            'category' => 'Perawatan Armada',
            'amount' => 1850000.00,
            'transaction_date' => Carbon::today()->subDays(2),
            'description' => 'Penggantian filter air, pembersihan tangki food-grade armada B 9301 TTE',
            'created_by' => $admin->id,
        ]);

        FinancialTransaction::create([
            'type' => 'expense',
            'category' => 'Tol/Uang Jalan',
            'amount' => 250000.00,
            'transaction_date' => Carbon::today(),
            'description' => 'Uang saku dan e-toll rute pengiriman Jakarta-Bekasi',
            'created_by' => $admin->id,
        ]);

        // 8. Activity Logs
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'SYSTEM_INIT',
            'description' => 'Sistem Water Tanker Management Logistics diinisialisasi.',
            'ip_address' => '127.0.0.1',
        ]);
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'ORDER_APPROVED',
            'description' => "Menyetujui pesanan {$ord2->order_number} dan menugaskan Driver Budi Santoso serta armada B 9201 TTC.",
            'ip_address' => '127.0.0.1',
        ]);
    }
}
