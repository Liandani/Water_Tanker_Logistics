<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Customers Table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('contact_person');
            $table->text('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('business_category')->default('Lainnya'); // Hotel, Proyek Konstruksi, Pabrik/Industri, Restoran, Lainnya
            $table->timestamps();
        });

        // 2. Drivers Table
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('license_number')->nullable(); // SIM
            $table->string('identity_number')->nullable(); // KTP
            $table->string('availability_status')->default('Aktif'); // Aktif, Libur, On Delivery
            $table->string('verification_status')->default('pending'); // pending, verified, rejected
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->timestamp('last_location_update')->nullable();
            $table->timestamps();
        });

        // 3. Vehicles Table (Armada Tangki)
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->unsignedInteger('capacity'); // e.g. 7000, 8000, 16000, 20000, 30000
            $table->string('vehicle_type'); // Tangki 7.000 L, Tangki 8.000 L, Tangki 16.000 L, Tangki 20.000 L, XXL / Multiple Trip
            $table->string('brand_model')->nullable(); // e.g. Hino 500, Mitsubishi Fuso Canter
            $table->string('status')->default('Ready'); // Ready, On Duty, Maintenance
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Tariff Settings Table
        Schema::create('tariff_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('water_price_per_liter', 10, 2)->default(50.00); // Rp 50/L
            $table->decimal('delivery_price_per_km', 10, 2)->default(15000.00); // Rp 15.000/KM
            $table->decimal('base_fee', 10, 2)->default(50000.00); // Rp 50.000 base fee
            $table->string('company_name')->default('Tirta Segar Mandiri Tanker Logistics');
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->decimal('depot_latitude', 10, 7)->default(-6.2088); // Default Jakarta depot
            $table->decimal('depot_longitude', 10, 7)->default(106.8456);
            $table->timestamps();
        });

        // 5. Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('volume'); // Liter
            $table->string('fleet_category_recommendation'); // E.g., Tangki 7.000 L, Tangki 16.000 L, XXL / Beberapa Trip
            $table->unsignedInteger('trips_count')->default(1);
            $table->text('delivery_address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->date('delivery_date');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('estimated_distance', 8, 2)->default(0); // in KM
            $table->decimal('water_price_rate', 10, 2)->default(0); // snapshot
            $table->decimal('delivery_price_rate', 10, 2)->default(0); // snapshot
            $table->decimal('water_price', 12, 2)->default(0);
            $table->decimal('delivery_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('status')->default('Pending'); // Pending, Diproses, Dikirim, Selesai, Dibatalkan, Pending Review
            $table->string('payment_status')->default('Unpaid'); // Unpaid, Down Payment, Paid
            $table->text('pending_reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 6. Assignments Table
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('Assigned'); // Assigned, Accepted, Rejected, In Transit, Filling Water, Completed, Cancelled
            $table->text('driver_reject_reason')->nullable();
            $table->text('driver_issue_reason')->nullable();
            $table->timestamps();
        });

        // 7. Delivery Locations Table (Tracking Breadcrumbs)
        Schema::create('delivery_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed', 6, 2)->nullable();
            $table->decimal('heading', 6, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // 8. Delivery Proofs Table (Bukti Pengiriman)
        Schema::create('delivery_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('recipient_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at');
            $table->timestamps();
        });

        // 9. Delivery Documents Table (Surat Izin Jalan / SJJ)
        Schema::create('delivery_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->string('document_number')->unique(); // E.g., SJJ/2026/11/0001
            $table->unsignedInteger('revision')->default(1);
            $table->string('pdf_path')->nullable();
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 10. Financial Transactions Table
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // income, expense
            $table->string('category'); // Penjualan Air, Bensin/Solar, Perawatan Armada, Servis, Gaji/Bonus Driver, Tol/Uang Jalan, Lainnya
            $table->decimal('amount', 14, 2);
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->string('receipt_photo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 11. Ratings Table
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('review')->nullable();
            $table->timestamps();
        });

        // 12. Activity Logs (Audit Log)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. ORDER_APPROVED, ORDER_ASSIGNED, etc.
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('delivery_documents');
        Schema::dropIfExists('delivery_proofs');
        Schema::dropIfExists('delivery_locations');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tariff_settings');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('customers');
    }
};

