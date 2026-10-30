# Data Seeder Sistem Manajemen Les (LKP Sang Siroju Lillah)

Dokumen ini berisi data mentah (*seed data*) dari database untuk digunakan pada seeder proyek baru (Laravel).

## 1. Data Cabang (`cabang`)

| ID | Nama Cabang | Alamat | Status |
|---|---|---|---|
| `cab-1` | Cabang Jakarta Selatan | Jl. Contoh No.1 | aktif |
| `cab-ponokawan` | Cabang Ponokawan | Krian, Sidoarjo | aktif |

---

## 2. Data Akun Pengguna (`users`)

> **Password default di sistem lama:**
> - Role `pusat`: `admin123`
> - Role `cabang`: `cabang123`
> - Role `guru`: `1995-05-15` (format YYYY-MM-DD dari tanggal lahir)

| ID | Nama | Email | NIP / No Pegawai | Tanggal Lahir | Role | Cabang ID | Teacher ID |
|---|---|---|---|---|---|---|---|
| `usr-admin` | Admin Pusat | admin@lespintar.id | - | - | `pusat` | - | - |
| `usr-cab-jaksel` | Pimpinan Cabang Jakarta Selatan | cabang.jaksel@lespintar.id | - | - | `cabang` | `cab-1` | - |
| `usr-cab-ponokawan` | Pimpinan Cabang Ponokawan | cabang.ponokawan@lespintar.id | - | - | `cabang` | `cab-ponokawan` | - |
| `usr-1791422335575-9nrv4` | Bu Divani | nip3101@lespintar.id | NIP-3101 | 1995-05-14 | `guru` | - | `guru-1791422335575-qphc8` |
| `usr-1791422335907-q99sh` | Bu Nida | nip3102@lespintar.id | NIP-3102 | 1995-05-14 | `guru` | - | `guru-1791422335907-5tetv` |
| `usr-1791422336165-8ljvo` | Bu Annisa | nip3103@lespintar.id | NIP-3103 | 1995-05-14 | `guru` | - | `guru-1791422336165-gkobr` |
| `usr-1791422336412-9gj5s` | Bu Ruli | nip3104@lespintar.id | NIP-3104 | 1995-05-14 | `guru` | - | `guru-1791422336412-ew2zf` |
| `usr-1791422336706-l4258` | Bu Salina | nip3105@lespintar.id | NIP-3105 | 1995-05-14 | `guru` | - | `guru-1791422336706-zxuom` |
| `usr-1791422336936-crc6u` | Bu Vita | nip3106@lespintar.id | NIP-3106 | 1995-05-14 | `guru` | - | `guru-1791422336935-tbcg6` |
| `usr-1791422337167-7da4s` | Bu Sofy | nip3107@lespintar.id | NIP-3107 | 1995-05-14 | `guru` | - | `guru-1791422337167-9ez08` |
| `usr-1791422337402-6nyar` | Bu Yessi | nip3108@lespintar.id | NIP-3108 | 1995-05-14 | `guru` | - | `guru-1791422337402-8ol8s` |
| `usr-1791422337635-ubzif` | Bu Ria | nip3109@lespintar.id | NIP-3109 | 1995-05-14 | `guru` | - | `guru-1791422337635-rn9qk` |
| `usr-1791422337892-29igk` | Bu Dina | nip3110@lespintar.id | NIP-3110 | 1995-05-14 | `guru` | - | `guru-1791422337892-xqwyv` |
| `usr-1791422338144-wf2x1` | Bu Erika | nip3111@lespintar.id | NIP-3111 | 1995-05-14 | `guru` | - | `guru-1791422338144-lafu0` |
| `usr-1791422338388-npncf` | Bu Sasa | nip3112@lespintar.id | NIP-3112 | 1995-05-14 | `guru` | - | `guru-1791422338388-f2beq` |

---

## 3. Data Guru (`guru`)

| ID Guru | User ID | NIP | Nama Guru | Email | No Telp / WA | Jenjang Utama | Multi-Jenjang | Cabang |
|---|---|---|---|---|---|---|---|---|
| `guru-1791422335575-qphc8` | `usr-1791422335575-9nrv4` | NIP-3101 | Bu Divani | nip3101@lespintar.id | 081562526895 | `TK` | `TK,SD` | `cab-ponokawan` |
| `guru-1791422335907-5tetv` | `usr-1791422335907-q99sh` | NIP-3102 | Bu Nida | nip3102@lespintar.id | 081830333456 | `SD` | `SD,SMP,SMA_SMK` | `cab-ponokawan` |
| `guru-1791422336165-gkobr` | `usr-1791422336165-8ljvo` | NIP-3103 | Bu Annisa | nip3103@lespintar.id | 081232321610 | `SD` | `SD` | `cab-ponokawan` |
| `guru-1791422336412-ew2zf` | `usr-1791422336412-9gj5s` | NIP-3104 | Bu Ruli | nip3104@lespintar.id | 081565794137 | `TK` | `TK` | `cab-ponokawan` |
| `guru-1791422336706-zxuom` | `usr-1791422336706-l4258` | NIP-3105 | Bu Salina | nip3105@lespintar.id | 081843296121 | `SD` | `SD` | `cab-ponokawan` |
| `guru-1791422336935-tbcg6` | `usr-1791422336936-crc6u` | NIP-3106 | Bu Vita | nip3106@lespintar.id | 081553098862 | `SD` | `SD` | `cab-ponokawan` |
| `guru-1791422337167-9ez08` | `usr-1791422337167-7da4s` | NIP-3107 | Bu Sofy | nip3107@lespintar.id | 081892541929 | `SD` | `SD` | `cab-ponokawan` |
| `guru-1791422337402-8ol8s` | `usr-1791422337402-6nyar` | NIP-3108 | Bu Yessi | nip3108@lespintar.id | 082130022764 | `SD` | `SD` | `cab-ponokawan` |
| `guru-1791422337635-rn9qk` | `usr-1791422337635-ubzif` | NIP-3109 | Bu Ria | nip3109@lespintar.id | 081838827935 | `SD` | `SD,SMP` | `cab-ponokawan` |
| `guru-1791422337892-xqwyv` | `usr-1791422337892-29igk` | NIP-3110 | Bu Dina | nip3110@lespintar.id | 082256671888 | `SMP` | `SMP,SMA_SMK` | `cab-ponokawan` |
| `guru-1791422338144-lafu0` | `usr-1791422338144-wf2x1` | NIP-3111 | Bu Erika | nip3111@lespintar.id | 081581819620 | `SMP` | `SMP,SMA_SMK` | `cab-ponokawan` |
| `guru-1791422338388-f2beq` | `usr-1791422338388-npncf` | NIP-3112 | Bu Sasa | nip3112@lespintar.id | 081646495050 | `SD` | `SD,SMA_SMK` | `cab-ponokawan` |

---

## 4. Data Siswa (`siswa`) - Total: 80 Siswa

| No | Cabang | Nama Siswa | Tempat Lahir | Tgl Lahir | Jenjang | Kls | Nama Ibu | No WA Ortu | Guru Pembimbing | Status | Alamat |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | `cab-ponokawan` | Alifa | Surabaya | 2022-07-26 | `TK` | 0 | Dian Permata | 081700000003 | Bu Divani | `aktif` | Dusun Krajan, RT 01/RW 04, No. 71, Ponokawan, Krian, Sidoarjo |
| 2 | `cab-ponokawan` | Daffa | Prambon | 2014-05-14 | `TK` | 0 | Indah Puspitasari | 081500000017 | Bu Ruli | `aktif` | Dusun Ponokawan, RT 06/RW 04, No. 44, Ponokawan, Krian, Sidoarjo |
| 3 | `cab-ponokawan` | Faris | Prambon | 2017-11-03 | `TK` | 0 | Lilis Kurniawati | 081300000005 | Bu Ruli | `aktif` | Dusun Karangnongko, RT 07/RW 03, No. 81, Ponokawan, Krian, Sidoarjo |
| 4 | `cab-ponokawan` | Ghanim | Tarik | 2012-01-04 | `TK` | 0 | Dian Permata | 081800000002 | Bu Ruli | `aktif` | Dusun Jatirejo, RT 04/RW 06, No. 25, Ponokawan, Krian, Sidoarjo |
| 5 | `cab-ponokawan` | Ibra | Jombang | 2015-03-04 | `TK` | 0 | Tika Pratiwi | 082200000019 | Bu Ruli | `aktif` | Dusun Jatirejo, RT 03/RW 01, No. 49, Ponokawan, Krian, Sidoarjo |
| 6 | `cab-ponokawan` | Jevan | Gresik | 2012-10-03 | `TK` | 0 | Tika Pratiwi | 081600000020 | Bu Divani | `aktif` | Dusun Ponokawan, RT 08/RW 04, No. 21, Ponokawan, Krian, Sidoarjo |
| 7 | `cab-ponokawan` | Naira | Mojokerto | 2017-12-04 | `TK` | 0 | Indah Puspitasari | 082100000016 | Bu Divani | `aktif` | Dusun Jatirejo, RT 08/RW 02, No. 65, Ponokawan, Krian, Sidoarjo |
| 8 | `cab-ponokawan` | Rangga | Prambon | 2022-09-01 | `TK` | 0 | Nita Anggraini | 081900000011 | Bu Divani | `aktif` | Dusun Krajan, RT 03/RW 02, No. 30, Ponokawan, Krian, Sidoarjo |
| 9 | `cab-ponokawan` | Rayyan | Krian | 2022-08-25 | `TK` | 0 | Novi Andriani | 081800000018 | Bu Divani | `aktif` | Dusun Balong, RT 08/RW 03, No. 69, Ponokawan, Krian, Sidoarjo |
| 10 | `cab-ponokawan` | Shakti | Prambon | 2015-06-07 | `TK` | 0 | Nur Aisyah | 081200000001 | Bu Divani | `aktif` | Dusun Karangnongko, RT 03/RW 04, No. 98, Ponokawan, Krian, Sidoarjo |
| 11 | `cab-ponokawan` | Zubair | Sidoarjo | 2013-04-19 | `TK` | 0 | Mia Rahmawati | 082200000015 | Bu Ruli | `aktif` | Dusun Krajan, RT 07/RW 01, No. 82, Ponokawan, Krian, Sidoarjo |
| 12 | `cab-ponokawan` | Akmal | Krian | 2020-08-26 | `SD` | 1 | Eka Susanti | 081300000021 | Bu Salina | `aktif` | Dusun Balong, RT 03/RW 06, No. 82, Ponokawan, Krian, Sidoarjo |
| 13 | `cab-ponokawan` | Attar | Jombang | 2012-05-16 | `SD` | 1 | Dian Permata | 081800000022 | Bu Vita | `aktif` | Dusun Krajan, RT 01/RW 04, No. 18, Ponokawan, Krian, Sidoarjo |
| 14 | `cab-ponokawan` | Aji | Surabaya | 2018-09-21 | `SD` | 2 | Nita Anggraini | 081500000025 | Bu Annisa | `aktif` | Dusun Ponokawan, RT 01/RW 06, No. 88, Ponokawan, Krian, Sidoarjo |
| 15 | `cab-ponokawan` | Alman | Gresik | 2020-06-27 | `SD` | 2 | Anita Rahma | 081100000033 | Bu Vita | `aktif` | Dusun Jatirejo, RT 06/RW 02, No. 73, Ponokawan, Krian, Sidoarjo |
| 16 | `cab-ponokawan` | Azril | Tarik | 2020-11-09 | `SD` | 2 | Rika Setyaningsih | 082100000031 | Bu Annisa | `aktif` | Dusun Ponokawan, RT 03/RW 05, No. 17, Ponokawan, Krian, Sidoarjo |
| 17 | `cab-ponokawan` | Erika | Mojokerto | 2016-06-03 | `SD` | 2 | Rina Wulandari | 081200000028 | Bu Sofy | `aktif` | Dusun Kedung, RT 05/RW 05, No. 14, Ponokawan, Krian, Sidoarjo |
| 18 | `cab-ponokawan` | Gavin | Gresik | 2015-08-09 | `SD` | 2 | Winda Safitri | 081800000034 | Bu Sofy | `aktif` | Dusun Balong, RT 01/RW 03, No. 44, Ponokawan, Krian, Sidoarjo |
| 19 | `cab-ponokawan` | Ghani | Sidoarjo | 2018-03-01 | `SD` | 2 | Maya Sari | 081700000026 | Bu Salina | `aktif` | Dusun Kedung, RT 03/RW 07, No. 58, Ponokawan, Krian, Sidoarjo |
| 20 | `cab-ponokawan` | Hira | Sidoarjo | 2012-01-19 | `SD` | 2 | Mia Rahmawati | 081900000024 | Bu Yessi | `aktif` | Dusun Karangnongko, RT 07/RW 04, No. 99, Ponokawan, Krian, Sidoarjo |
| 21 | `cab-ponokawan` | Nafisa | Tarik | 2017-06-09 | `SD` | 2 | Rika Setyaningsih | 081600000027 | Bu Vita | `aktif` | Dusun Balong, RT 02/RW 02, No. 72, Ponokawan, Krian, Sidoarjo |
| 22 | `cab-ponokawan` | Naura | Krian | 2018-05-02 | `SD` | 2 | Nur Aisyah | 081700000030 | Bu Sasa | `aktif` | Dusun Kedung, RT 03/RW 02, No. 45, Ponokawan, Krian, Sidoarjo |
| 23 | `cab-ponokawan` | Rendra | Gresik | 2020-11-06 | `SD` | 2 | Lina Marlina | 081900000010 | Bu Vita | `aktif` | Dusun Krajan, RT 01/RW 08, No. 98, Ponokawan, Krian, Sidoarjo |
| 24 | `cab-ponokawan` | Syafira | Mojokerto | 2017-11-09 | `SD` | 2 | Fitri Handayani | 081100000023 | Bu Sofy | `aktif` | Dusun Jatirejo, RT 08/RW 03, No. 13, Ponokawan, Krian, Sidoarjo |
| 25 | `cab-ponokawan` | Zizi | Tarik | 2017-04-19 | `SD` | 2 | Winda Safitri | 081300000029 | Bu Yessi | `aktif` | Dusun Krajan, RT 02/RW 06, No. 39, Ponokawan, Krian, Sidoarjo |
| 26 | `cab-ponokawan` | Aisyah | Mojokerto | 2015-06-07 | `SD` | 3 | Winda Safitri | 081300000038 | Bu Salina | `aktif` | Dusun Krajan, RT 01/RW 06, No. 20, Ponokawan, Krian, Sidoarjo |
| 27 | `cab-ponokawan` | Eza | Prambon | 2015-03-14 | `SD` | 3 | Nita Anggraini | 082100000004 | Bu Annisa | `aktif` | Dusun Jatirejo, RT 01/RW 08, No. 50, Ponokawan, Krian, Sidoarjo |
| 28 | `cab-ponokawan` | Inara | Surabaya | 2019-11-12 | `SD` | 3 | Rina Wulandari | 081300000037 | Bu Annisa | `aktif` | Dusun Ponokawan, RT 07/RW 01, No. 8, Ponokawan, Krian, Sidoarjo |
| 29 | `cab-ponokawan` | Rayyan | Jombang | 2013-06-17 | `SD` | 3 | Dwi Astuti | 081100000036 | Bu Sasa | `aktif` | Dusun Ponokawan, RT 08/RW 05, No. 29, Ponokawan, Krian, Sidoarjo |
| 30 | `cab-ponokawan` | Zaki | Jombang | 2021-02-05 | `SD` | 3 | Dwi Astuti | 081300000035 | Bu Yessi | `aktif` | Dusun Ponokawan, RT 08/RW 02, No. 33, Ponokawan, Krian, Sidoarjo |
| 31 | `cab-ponokawan` | Abid | Tarik | 2022-09-13 | `SD` | 4 | Eka Susanti | 081700000039 | Bu Vita | `aktif` | Dusun Krajan, RT 01/RW 06, No. 75, Ponokawan, Krian, Sidoarjo |
| 32 | `cab-ponokawan` | Alzio | Prambon | 2021-10-23 | `SD` | 4 | Lina Marlina | 081900000042 | Bu Sasa | `aktif` | Dusun Kedung, RT 03/RW 06, No. 19, Ponokawan, Krian, Sidoarjo |
| 33 | `cab-ponokawan` | Dani | Jombang | 2012-10-04 | `SD` | 4 | Eka Susanti | 081600000043 | Bu Annisa | `aktif` | Dusun Balong, RT 03/RW 06, No. 56, Ponokawan, Krian, Sidoarjo |
| 34 | `cab-ponokawan` | Farel | Jombang | 2021-09-22 | `SD` | 4 | Novi Andriani | 081500000012 | Bu Sofy | `aktif` | Dusun Balong, RT 03/RW 04, No. 67, Ponokawan, Krian, Sidoarjo |
| 35 | `cab-ponokawan` | Messi | Gresik | 2018-04-23 | `SD` | 4 | Dewi Lestari | 081100000041 | Bu Yessi | `aktif` | Dusun Karangnongko, RT 03/RW 07, No. 67, Ponokawan, Krian, Sidoarjo |
| 36 | `cab-ponokawan` | Nizam | Gresik | 2017-09-18 | `SD` | 4 | Rina Wulandari | 081900000040 | Bu Sofy | `aktif` | Dusun Karangnongko, RT 02/RW 03, No. 18, Ponokawan, Krian, Sidoarjo |
| 37 | `cab-ponokawan` | Syafa | Gresik | 2016-12-09 | `SD` | 4 | Tika Pratiwi | 081200000044 | Bu Salina | `aktif` | Dusun Krajan, RT 08/RW 06, No. 75, Ponokawan, Krian, Sidoarjo |
| 38 | `cab-ponokawan` | Yohan | Mojokerto | 2017-12-07 | `SD` | 4 | Novi Andriani | 081500000032 | Bu Salina | `aktif` | Dusun Krajan, RT 02/RW 06, No. 65, Ponokawan, Krian, Sidoarjo |
| 39 | `cab-ponokawan` | Agam | Tarik | 2017-11-14 | `SD` | 5 | Dwi Astuti | 082100000009 | Bu Salina | `aktif` | Dusun Jatirejo, RT 06/RW 08, No. 34, Ponokawan, Krian, Sidoarjo |
| 40 | `cab-ponokawan` | Alca | Mojokerto | 2011-10-16 | `SD` | 5 | Nur Aisyah | 081900000045 | Bu Vita | `aktif` | Dusun Krajan, RT 07/RW 04, No. 80, Ponokawan, Krian, Sidoarjo |
| 41 | `cab-ponokawan` | Azka | Krian | 2015-06-21 | `SD` | 5 | Indah Puspitasari | 081700000046 | Bu Sofy | `aktif` | Dusun Ponokawan, RT 07/RW 05, No. 95, Ponokawan, Krian, Sidoarjo |
| 42 | `cab-ponokawan` | Azkiya | Prambon | 2017-02-10 | `SD` | 5 | Rina Wulandari | 081800000078 | - | `aktif` | Dusun Ponokawan, RT 02/RW 07, No. 58, Ponokawan, Krian, Sidoarjo |
| 43 | `cab-ponokawan` | Dinda | Gresik | 2017-10-25 | `SD` | 5 | Lina Marlina | 081200000079 | - | `aktif` | Dusun Balong, RT 03/RW 01, No. 94, Ponokawan, Krian, Sidoarjo |
| 44 | `cab-ponokawan` | Gibran | Surabaya | 2010-10-08 | `SD` | 5 | Nita Anggraini | 081300000080 | - | `aktif` | Dusun Karangnongko, RT 07/RW 06, No. 98, Ponokawan, Krian, Sidoarjo |
| 45 | `cab-ponokawan` | Alivia | Krian | 2020-12-09 | `SD` | 6 | Dwi Astuti | 081600000047 | Bu Yessi | `aktif` | Dusun Krajan, RT 02/RW 01, No. 11, Ponokawan, Krian, Sidoarjo |
| 46 | `cab-ponokawan` | Alvaro | Sidoarjo | 2015-05-15 | `SD` | 6 | Indah Puspitasari | 081300000056 | - | `aktif` | Dusun Ponokawan, RT 07/RW 05, No. 38, Ponokawan, Krian, Sidoarjo |
| 47 | `cab-ponokawan` | Arya | Surabaya | 2022-09-20 | `SD` | 6 | Winda Safitri | 082100000013 | Bu Yessi | `aktif` | Dusun Ponokawan, RT 03/RW 05, No. 21, Ponokawan, Krian, Sidoarjo |
| 48 | `cab-ponokawan` | Azzamy | Krian | 2012-08-09 | `SD` | 6 | Dwi Astuti | 082200000061 | - | `aktif` | Dusun Krajan, RT 06/RW 08, No. 13, Ponokawan, Krian, Sidoarjo |
| 49 | `cab-ponokawan` | Elfiano | Gresik | 2018-08-22 | `SD` | 6 | Yuni Kartika | 081700000062 | - | `aktif` | Dusun Ponokawan, RT 07/RW 02, No. 69, Ponokawan, Krian, Sidoarjo |
| 50 | `cab-ponokawan` | Faris | Sidoarjo | 2021-11-19 | `SD` | 6 | Dian Permata | 081700000048 | Bu Sasa | `aktif` | Dusun Jatirejo, RT 06/RW 04, No. 86, Ponokawan, Krian, Sidoarjo |
| 51 | `cab-ponokawan` | Nurma | Surabaya | 2022-05-10 | `SD` | 6 | Eka Susanti | 081200000050 | - | `aktif` | Dusun Krajan, RT 04/RW 01, No. 38, Ponokawan, Krian, Sidoarjo |
| 52 | `cab-ponokawan` | Rafif | Krian | 2020-11-16 | `SD` | 6 | Eka Susanti | 081700000060 | - | `aktif` | Dusun Jatirejo, RT 04/RW 05, No. 89, Ponokawan, Krian, Sidoarjo |
| 53 | `cab-ponokawan` | Raiya | Tarik | 2019-12-06 | `SD` | 6 | Rika Setyaningsih | 081300000049 | - | `aktif` | Dusun Jatirejo, RT 01/RW 02, No. 89, Ponokawan, Krian, Sidoarjo |
| 54 | `cab-ponokawan` | Yoga | Mojokerto | 2020-03-05 | `SD` | 6 | Dewi Lestari | 082200000014 | Bu Annisa | `aktif` | Dusun Balong, RT 03/RW 06, No. 52, Ponokawan, Krian, Sidoarjo |
| 55 | `cab-ponokawan` | Abi | Mojokerto | 2017-08-06 | `SMP` | 7 | Anita Rahma | 081100000068 | Bu Dina | `aktif` | Dusun Krajan, RT 05/RW 06, No. 51, Ponokawan, Krian, Sidoarjo |
| 56 | `cab-ponokawan` | Abriele | Surabaya | 2015-10-01 | `SMP` | 7 | Nur Aisyah | 081900000008 | Bu Nida | `aktif` | Dusun Jatirejo, RT 02/RW 06, No. 41, Ponokawan, Krian, Sidoarjo |
| 57 | `cab-ponokawan` | Galih | Tarik | 2022-09-25 | `SMP` | 7 | Mia Rahmawati | 081200000067 | Bu Ria | `aktif` | Dusun Kedung, RT 05/RW 08, No. 93, Ponokawan, Krian, Sidoarjo |
| 58 | `cab-ponokawan` | Zafira | Krian | 2018-07-05 | `SMP` | 7 | Anita Rahma | 081200000066 | Bu Nida | `aktif` | Dusun Krajan, RT 08/RW 06, No. 51, Ponokawan, Krian, Sidoarjo |
| 59 | `cab-ponokawan` | Audy | Jombang | 2011-12-03 | `SMP` | 8 | Rina Wulandari | 081500000073 | Bu Erika | `aktif` | Dusun Jatirejo, RT 07/RW 08, No. 74, Ponokawan, Krian, Sidoarjo |
| 60 | `cab-ponokawan` | Aura | Mojokerto | 2020-08-18 | `SMP` | 8 | Dian Permata | 082200000072 | Bu Dina | `aktif` | Dusun Krajan, RT 01/RW 04, No. 5, Ponokawan, Krian, Sidoarjo |
| 61 | `cab-ponokawan` | Cantika | Prambon | 2014-08-27 | `SMP` | 8 | Tika Pratiwi | 081700000071 | Bu Ria | `aktif` | Dusun Karangnongko, RT 08/RW 07, No. 35, Ponokawan, Krian, Sidoarjo |
| 62 | `cab-ponokawan` | Monic | Tarik | 2022-05-21 | `SMP` | 8 | Lina Marlina | 081300000069 | Bu Erika | `aktif` | Dusun Balong, RT 07/RW 01, No. 11, Ponokawan, Krian, Sidoarjo |
| 63 | `cab-ponokawan` | Putra | Tarik | 2013-04-02 | `SMP` | 8 | Siti Aminah | 081700000057 | Bu Dina | `aktif` | Dusun Kedung, RT 02/RW 02, No. 91, Ponokawan, Krian, Sidoarjo |
| 64 | `cab-ponokawan` | Raga | Jombang | 2021-08-04 | `SMP` | 8 | Maya Sari | 082200000007 | Bu Ria | `aktif` | Dusun Krajan, RT 04/RW 04, No. 12, Ponokawan, Krian, Sidoarjo |
| 65 | `cab-ponokawan` | Reyhan | Surabaya | 2016-09-17 | `SMP` | 8 | Rina Wulandari | 082200000058 | Bu Erika | `aktif` | Dusun Karangnongko, RT 07/RW 07, No. 58, Ponokawan, Krian, Sidoarjo |
| 66 | `cab-ponokawan` | Tata | Krian | 2020-12-14 | `SMP` | 8 | Eka Susanti | 081200000070 | Bu Nida | `aktif` | Dusun Jatirejo, RT 03/RW 02, No. 48, Ponokawan, Krian, Sidoarjo |
| 67 | `cab-ponokawan` | Intan | Jombang | 2010-01-07 | `SMP` | 9 | Dewi Lestari | 082100000055 | Bu Ria | `aktif` | Dusun Kedung, RT 01/RW 06, No. 77, Ponokawan, Krian, Sidoarjo |
| 68 | `cab-ponokawan` | Nada | Prambon | 2017-10-08 | `SMP` | 9 | Fitri Handayani | 081200000053 | Bu Erika | `aktif` | Dusun Jatirejo, RT 08/RW 08, No. 28, Ponokawan, Krian, Sidoarjo |
| 69 | `cab-ponokawan` | Naura | Sidoarjo | 2017-08-01 | `SMP` | 9 | Lilis Kurniawati | 082200000006 | Bu Ria | `aktif` | Dusun Jatirejo, RT 04/RW 08, No. 86, Ponokawan, Krian, Sidoarjo |
| 70 | `cab-ponokawan` | Rafa | Jombang | 2012-09-22 | `SMP` | 9 | Eka Susanti | 081500000052 | Bu Dina | `aktif` | Dusun Jatirejo, RT 08/RW 05, No. 87, Ponokawan, Krian, Sidoarjo |
| 71 | `cab-ponokawan` | Rifky | Gresik | 2018-02-10 | `SMP` | 9 | Dwi Astuti | 082200000051 | Bu Ria | `aktif` | Dusun Krajan, RT 04/RW 04, No. 38, Ponokawan, Krian, Sidoarjo |
| 72 | `cab-ponokawan` | Venia | Gresik | 2013-01-20 | `SMP` | 9 | Siti Aminah | 081600000054 | Bu Nida | `aktif` | Dusun Ponokawan, RT 08/RW 02, No. 3, Ponokawan, Krian, Sidoarjo |
| 73 | `cab-ponokawan` | Adinda | Jombang | 2021-08-31 | `SMA_SMK` | 10 | Nita Anggraini | 081300000077 | Bu Sasa | `aktif` | Dusun Krajan, RT 01/RW 05, No. 67, Ponokawan, Krian, Sidoarjo |
| 74 | `cab-ponokawan` | Alisha | Tarik | 2012-08-02 | `SMA_SMK` | 10 | Dwi Astuti | 081100000074 | Bu Nida | `aktif` | Dusun Jatirejo, RT 01/RW 06, No. 69, Ponokawan, Krian, Sidoarjo |
| 75 | `cab-ponokawan` | Fadhli | Surabaya | 2011-10-10 | `SMA_SMK` | 10 | Mia Rahmawati | 081800000059 | Bu Nida | `aktif` | Dusun Balong, RT 06/RW 02, No. 28, Ponokawan, Krian, Sidoarjo |
| 76 | `cab-ponokawan` | Adhe | Prambon | 2016-10-24 | `SMA_SMK` | 11 | Lina Marlina | 081700000075 | Bu Dina | `aktif` | Dusun Karangnongko, RT 01/RW 07, No. 63, Ponokawan, Krian, Sidoarjo |
| 77 | `cab-ponokawan` | Danty | Tarik | 2017-10-24 | `SMA_SMK` | 11 | Indah Puspitasari | 081900000063 | Bu Dina | `aktif` | Dusun Jatirejo, RT 02/RW 07, No. 49, Ponokawan, Krian, Sidoarjo |
| 78 | `cab-ponokawan` | Vima | Tarik | 2014-03-31 | `SMA_SMK` | 11 | Winda Safitri | 081100000076 | Bu Erika | `aktif` | Dusun Kedung, RT 01/RW 01, No. 85, Ponokawan, Krian, Sidoarjo |
| 79 | `cab-ponokawan` | Bilqis | Gresik | 2010-05-26 | `SMA_SMK` | 12 | Mia Rahmawati | 081900000065 | Bu Sasa | `aktif` | Dusun Karangnongko, RT 02/RW 02, No. 43, Ponokawan, Krian, Sidoarjo |
| 80 | `cab-ponokawan` | Lionel | Jombang | 2017-08-12 | `SMA_SMK` | 12 | Tika Pratiwi | 081100000064 | Bu Erika | `aktif` | Dusun Ponokawan, RT 03/RW 06, No. 11, Ponokawan, Krian, Sidoarjo |

---

## 5. Data Master Tarif SPP Global (`tarif_spp`)

| ID | Jenjang | Kelas Min | Kelas Max | Nominal (Rp) | Berlaku Sejak |
|---|---|---|---|---|---|
| `trf-tk` | `TK` | 0 | 0 | Rp 150.000 | 2025-12-31 |
| `trf-sd1` | `SD` | 1 | 3 | Rp 150.000 | 2025-12-31 |
| `trf-sd2` | `SD` | 4 | 6 | Rp 175.000 | 2025-12-31 |
| `trf-smp` | `SMP` | 7 | 9 | Rp 200.000 | 2025-12-31 |
| `trf-sma` | `SMA_SMK` | 10 | 12 | Rp 225.000 | 2025-12-31 |
