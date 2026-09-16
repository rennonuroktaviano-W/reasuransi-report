<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |--------------------------------------------------------------------------
    |
    | Berisi terjemahan Bahasa Indonesia yang ramah dan mudah dipahami untuk
    | seluruh pesan validasi bawaan Laravel.
    |
    */

    'accepted' => 'Kolom :attribute harus disetujui.',
    'accepted_if' => 'Kolom :attribute harus disetujui ketika :other adalah :value.',
    'active_url' => 'Kolom :attribute harus berupa URL yang valid.',
    'after' => 'Kolom :attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Kolom :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Kolom :attribute harus berupa array.',
    'ascii' => 'Kolom :attribute hanya boleh berisi karakter alfanumerik dan simbol satu byte.',
    'before' => 'Kolom :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Kolom :attribute harus memiliki antara :min sampai :max item.',
        'file' => 'Kolom :attribute harus antara :min sampai :max kilobyte.',
        'numeric' => 'Kolom :attribute harus antara :min sampai :max.',
        'string' => 'Kolom :attribute harus antara :min sampai :max karakter.',
    ],
    'boolean' => 'Kolom :attribute harus berupa true atau false.',
    'can' => 'Kolom :attribute berisi nilai yang tidak sah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => 'Kolom :attribute kehilangan nilai yang diwajibkan.',
    'current_password' => 'Kata sandi salah.',
    'date' => 'Format tanggal pada :attribute tidak valid.',
    'date_equals' => 'Kolom :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => 'Kolom :attribute harus sesuai format :format.',
    'decimal' => 'Kolom :attribute harus memiliki :decimal tempat desimal.',
    'declined' => 'Kolom :attribute harus ditolak.',
    'declined_if' => 'Kolom :attribute harus ditolak ketika :other adalah :value.',
    'different' => 'Kolom :attribute dan :other harus berbeda.',
    'digits' => 'Kolom :attribute harus :digits digit.',
    'digits_between' => 'Kolom :attribute harus antara :min sampai :max digit.',
    'dimensions' => 'Dimensi gambar pada :attribute tidak valid.',
    'distinct' => 'Kolom :attribute memiliki nilai duplikat.',
    'doesnt_end_with' => 'Kolom :attribute tidak boleh diakhiri dengan salah satu dari berikut: :values.',
    'doesnt_start_with' => 'Kolom :attribute tidak boleh diawali dengan salah satu dari berikut: :values.',
    'email' => 'Kolom :attribute harus berupa alamat email yang sah.',
    'ends_with' => 'Kolom :attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum' => 'Nilai yang dipilih pada :attribute tidak valid.',
    'exists' => 'Data :attribute yang dipilih tidak tersedia.',
    'extensions' => 'Kolom :attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => 'Kolom :attribute harus berupa file.',
    'filled' => 'Kolom :attribute harus diisi.',
    'gt' => [
        'array' => 'Kolom :attribute harus memiliki lebih dari :value item.',
        'file' => 'Kolom :attribute harus lebih besar dari :value kilobyte.',
        'numeric' => 'Kolom :attribute harus lebih besar dari :value.',
        'string' => 'Kolom :attribute harus lebih dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Kolom :attribute harus memiliki :value item atau lebih.',
        'file' => 'Kolom :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'numeric' => 'Kolom :attribute harus lebih besar dari atau sama dengan :value.',
        'string' => 'Kolom :attribute harus lebih dari atau sama dengan :value karakter.',
    ],
    'hex_color' => 'Kolom :attribute harus berupa warna heksadesimal yang valid.',
    'image' => 'Kolom :attribute harus berupa gambar.',
    'in' => 'Nilai yang dipilih pada :attribute tidak valid.',
    'in_array' => 'Kolom :attribute harus ada di dalam :other.',
    'integer' => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip' => 'Kolom :attribute harus berupa alamat IP yang valid.',
    'ipv4' => 'Kolom :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => 'Kolom :attribute harus berupa alamat IPv6 yang valid.',
    'json' => 'Kolom :attribute harus berupa teks JSON yang valid.',
    'list' => 'Kolom :attribute harus berupa daftar.',
    'lowercase' => 'Kolom :attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => 'Kolom :attribute harus memiliki kurang dari :value item.',
        'file' => 'Kolom :attribute harus kurang dari :value kilobyte.',
        'numeric' => 'Kolom :attribute harus kurang dari :value.',
        'string' => 'Kolom :attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Kolom :attribute tidak boleh lebih dari :value item.',
        'file' => 'Kolom :attribute tidak boleh lebih besar dari :value kilobyte.',
        'numeric' => 'Kolom :attribute tidak boleh lebih besar dari :value.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :value karakter.',
    ],
    'mac_address' => 'Kolom :attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => 'Kolom :attribute tidak boleh lebih dari :max item.',
        'file' => 'Kolom :attribute tidak boleh lebih dari :max kilobyte.',
        'numeric' => 'Kolom :attribute tidak boleh lebih besar dari :max.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits' => 'Kolom :attribute tidak boleh lebih dari :max digit.',
    'mimes' => 'Kolom :attribute harus berupa file dengan tipe: :values.',
    'mimetypes' => 'Kolom :attribute harus berupa file dengan tipe: :values.',
    'min' => [
        'array' => 'Kolom :attribute harus memiliki minimal :min item.',
        'file' => 'Kolom :attribute harus minimal :min kilobyte.',
        'numeric' => 'Kolom :attribute harus minimal :min.',
        'string' => 'Kolom :attribute harus minimal :min karakter.',
    ],
    'min_digits' => 'Kolom :attribute tidak boleh kurang dari :min digit.',
    'missing' => 'Kolom :attribute harus kosong.',
    'missing_if' => 'Kolom :attribute harus kosong ketika :other adalah :value.',
    'missing_unless' => 'Kolom :attribute harus kosong kecuali :other adalah :value.',
    'missing_with' => 'Kolom :attribute harus kosong saat :values ada.',
    'missing_with_all' => 'Kolom :attribute harus kosong saat semua :values ada.',
    'multiple_of' => 'Kolom :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Nilai yang dipilih pada :attribute tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'present' => 'Kolom :attribute harus tersedia.',
    'prohibited' => 'Kolom :attribute dilarang terisi.',
    'prohibited_if' => 'Kolom :attribute dilarang terisi ketika :other adalah :value.',
    'prohibited_unless' => 'Kolom :attribute dilarang terisi kecuali :other adalah :value.',
    'prohibits' => 'Kolom :attribute dilarang berisi bersama :other.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => 'Kolom :attribute wajib diisi.',
    'required_array_keys' => 'Kolom :attribute wajib berisi entri untuk: :values.',
    'required_if' => 'Kolom :attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => 'Kolom :attribute wajib diisi ketika :other disetujui.',
    'required_if_declined' => 'Kolom :attribute wajib diisi ketika :other ditolak.',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other adalah :value.',
    'required_with' => 'Kolom :attribute wajib diisi ketika :values ada.',
    'required_with_all' => 'Kolom :attribute wajib diisi ketika semua :values ada.',
    'required_without' => 'Kolom :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Kolom :attribute wajib diisi ketika tidak ada :values yang ada.',
    'same' => 'Kolom :attribute dan :other harus sama.',
    'size' => [
        'array' => 'Kolom :attribute harus berisi :size item.',
        'file' => 'Kolom :attribute harus :size kilobyte.',
        'numeric' => 'Kolom :attribute harus :size.',
        'string' => 'Kolom :attribute harus :size karakter.',
    ],
    'starts_with' => 'Kolom :attribute harus diawali dengan salah satu dari berikut: :values.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'timezone' => 'Kolom :attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan, silakan gunakan nomor atau nilai lain.',
    'uploaded' => 'Gagal mengunggah :attribute.',
    'uppercase' => 'Kolom :attribute harus berupa huruf kapital.',
    'url' => 'Format :attribute tidak valid.',
    'ulid' => 'Kolom :attribute harus berupa ULID yang valid.',
    'uuid' => 'Kolom :attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi Kustom
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'pesan-kustom',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribut Validasi Kustom
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'policy_number' => 'Nomor Polis',
        'insured_name' => 'Nama Tertanggung',
        'birth_date' => 'Tanggal Lahir',
        'sum_insured' => 'Uang Pertanggungan (UP Utama)',
        'retention' => 'Sendiri (Retention)',
        'ceded_amount' => 'UP Direasuransikan (Ceded)',
        'reinsurance_type' => 'Jenis Reasuransi',
        'reinsurance_premium' => 'Premi Reasuransi',
        'claim_number' => 'Nomor Klaim',
        'production_id' => 'Polis',
        'claim_cause' => 'Sebab Klaim',
        'total_claim_value' => 'Total Nilai Klaim',
        'reinsurance_recovery' => 'Recovery Reasuransi',
        'claim_status' => 'Status Klaim',
    ],
];
