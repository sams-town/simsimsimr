<?php class Kardex_stock_model extends MY_Model {
	protected $tableName = 'kardex_stock';
	protected $resultMode = 'object';
	public $primaryKey = 'id';
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();

	public function get_qty($pharmacy_catalog_id, $dose, $dose_unit_id, $signatura_times_id, $administer_id, $registration_destination_id = null) {
		$select = 'a.id, a.package_id, a.dose, a.dose_unit_id, a.packaging_value, a.packaging_unit_id';
		$catalog = $this->Catalog->select($pharmacy_catalog_id, $select);
		if (count($catalog) == 0) {
			return null;
		}

		// Define data master katalog
		$package_id = $catalog[0]->package_id;
		$catalog_dose = $catalog[0]->dose;
		$catalog_dose_unit_id = $catalog[0]->dose_unit_id;
		$packaging_value = $catalog[0]->packaging_value;
		$packaging_unit_id = $catalog[0]->packaging_unit_id;

		/* 
			Mendapatkan epoch time untuk pagi, siang, sore, malam, pagi besok 

			Menghitung berapa banyak obat yang harus diberikan atau berapa banyak jumlah yang diminta
			ke Farmasi dengan cara dari waktu order resep

			cth. Diresepkan parasetamol 1 tablet, tiap 8 jam dan diorder pukul 08.00
			maka rumus yang akan digunakan 1 tablet * 3 (minum 14.00, 22.00, 06.00 (besok))
			hasil 3 tablet yang diminta ke Farmasi
		*/
		$now = time();
		// $now = strtotime(date('Y-m-d') . ' 10:00:00');
		$morning           = jakartaTimestamp('06:00:00');
		$after_morning     = jakartaTimestamp('10:00:00');
		$noon              = jakartaTimestamp('14:00:00');
		$afternoon         = jakartaTimestamp('18:00:00');
		$evening           = jakartaTimestamp('22:00:00');
		$after_evening     = jakartaTimestamp('02:00:00', 1);
		$tomorrow_morning  = jakartaTimestamp('06:00:00', 1);

		if ($now > $morning && $now < $noon) {
			if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
				if ($now < $after_morning) {
					$times = 6;
				} else {
					$times = 5;
				}
			}

			if ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
				$times = 4;
			} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
				$times = 3;
			} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
				$times = 2;
			} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            	$times = 2;
			} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
				$times = 1;
			} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
				$times = 1;
			}
			// echo 'pagi menjelang siang';
		} elseif ($now > $noon && $now < $afternoon) {
			if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
				$times = 4;
			} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
				$times = 3;
			} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
				$times = 2;
			} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
				$times = 2;
			} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
				$times = 2;
			} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
				$times = 1;
			} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
				$times = 1;
			}
			// echo 'siang menjelang sore';
		} elseif ($now > $afternoon && $now < $evening) {
			if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
				$times = 4;
			} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
				$times = 2;
			} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
				$times = 2;
			} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
				$times = 2;
			} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
				$times = 2;
			} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
				$times = 1;
			} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
				$times = 1;
			}
			// echo 'sore menjelang malam';
		} elseif ($now > $evening && $now < $tomorrow_morning) {
			if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
				if ($now < $after_evening) {
					$times = 6;
				} else {
					$times = 5;
				}
			}
			if ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
				$times = 4;
			} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
				$times = 4;
			} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
				$times = 3;
			} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            	$times = 2;
			} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
				$times = 1;
			} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
				$times = 1;
			}
			// echo 'malam menjelang pagi';
		}

		// Langsung menghitung berdasarkan jumlah dari resep dokter dan waktu sekarang jika master katalog tidak lengkap
		if ($catalog_dose == null || $catalog_dose_unit_id == null || $packaging_value == null || $packaging_unit_id == null) {
			$qty = $dose * $times;
			return $qty;
		}

		/* 
			Mengubah satuan yang tidak bisa dihitung menjadi satuan yang dapat dihitung
			cth. 1 Tablet -> 300 MG
		*/
		$dose = clean_dose_unit($dose, $dose_unit_id, $pharmacy_catalog_id);
		$dose_unit_id = clean_unit($dose_unit_id);

		$drug_dosage_requirements = $dose * $times; // Menghitung kebutuhan dosis selama seharian penuh

		/* 
			Perhitungan stock asli
			
			Katalog obat dibagi menjadi 3 kategori:
			1. Obat sekali pakai, 1x masuk = 1 kemasan
				cth. 1 Tablet, 1 Ampul, 1 Vial
			2. Obat tidak habis sekali pakai dan resep menggunakan, 1 kemasan = bisa dipakai berkali-kali
				cth. dosis 15 ML dengan kemasan berisi 60ML
			3. Obat yang bersifat sulit dihitung
				cth. dosis dalam bentuk Oles atau Tabur

			- Rumus kategori pertama
				Jumlah stock sekarang = (Isi Kemasan * Times (perhitungan dari waktu order)) - Kebutuhan Dosis seharian
				Jumlah fisik sekarang = 1 * Times (perhitungan dari waktu order)
				- Jika diresepkan dengan MG dan tidak sama dengan satu kekuatan
					Jumlah stock sekarang = Satu Kekuatan
					Jumlah fisik sekarang = Jika Kebutuhan dosis seharian > Jumlah stock sekarang
					Jumlah fisik awal 1 dan akan ditambah dengan Satu Kekuatan satu per satu sampai kebutuhan dosis 
					seharian terpenuhi
					Jumlah stock sekarang = Jumlah stock sekarang - Kebutuhan dosis seharian

			- Rumus kategori kedua
				- Jika diresepkan dengan ML
					Jumlah stock sekarang = Isi Kemasan - Kebutuhan Dosis seharian
					Jumlah fisik sekarang = 1
				- Jika diresepkan dengan MG dan satuan kekuatan MG/ML
					Jumlah kekuatan = Kekuatan per ML * Isi Kemasan
					Jumlah stock sekarang = Jumlah kekuatan
					Jumlah fisik sekarang = Jika kebutuhan dosis seharian > Jumlah stock sekarang
					Jumlah fisik awal 1 dan akan ditambah satu per satu sampai kebutuhan dosis seharian terpenuhi
					
					Jika dosis dokter lebih besar dari kekuatan katalog
					Jumlah kekuatan = Jumlah kekuatan * Jumlah fisik sekarang

					Jumlah stock sekarang = Isi Kemasan * (Jumlah Kekuatan - Dosis Dokter) / Jumlah Kekuatan
		*/
		if ($package_id == '7afbf19d-209c-407f-adbf-17ce9c82728d') { // BOTOL
			$current_amount = $packaging_value - $drug_dosage_requirements;
			$current_physical_quantity = 1;
		} elseif (
			$dose_unit_id == 'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5' //	TABUR
			|| $dose_unit_id == 'f4d6a5a3-cc89-46c8-895d-c31417b27a78' //	SPRAY
			|| $dose_unit_id == '8ff1ce10-45cd-47fb-93bc-c6449946a099' //	TETES
			|| $dose_unit_id == 'e72b6358-8d74-447c-b5be-9623a1903470' // OLES
			|| $administer_id == '02d981b9-8644-479f-b5e1-465d695f066c' // Cara pakai dioleskan
			|| ($package_id == 'c37c026c-f4e5-4f32-a4ff-1525bbe74803' && $administer_id == '02d981b9-8644-479f-b5e1-465d695f066c') // TUBE & Cara pakai dioleskan
		) {
			$current_amount = $packaging_value;
			$current_physical_quantity = 1;
		} elseif (
			$package_id == '357310dd-904f-4b24-92a2-7987a75cdfc5' // TABLET
			|| $package_id == 'c5bc8af2-c4eb-4d74-9566-39048ba2f00a' // KAPSUL
			|| $package_id == 'c14d6d95-4513-45dc-956d-a8da288e0a21' // OVULA
			|| $package_id == '54f6e791-283d-479d-ba82-9a728cc5ce7b' // SACHET
			|| $package_id == 'a0c282f5-6ee2-4577-b3c8-bf0779e515f5' // SUPP
			|| $package_id == '12b26d53-86c1-49dd-940c-acff164c423b' // SUPPOS
			|| $package_id == '7b430ad7-7462-47d3-bed7-7fb649369771' // SYRINGE
			|| $package_id == 'c37c026c-f4e5-4f32-a4ff-1525bbe74803' // TUBE
			|| $package_id == '140c8ede-9b7c-4fb6-bda7-512bc94df6bf' // PCS
		) {
			$packaging_value = $packaging_value * $times;
			$current_amount = $packaging_value - $drug_dosage_requirements;
			$current_physical_quantity = 1 * $times;

			/* 
				Ketika dosis dokter tidak sama dengan satu kekuatan obat
				cth. katalog obat mengandung 100 MG
				dan dosis dari dokter 75 MG
			*/
			if ($dose != $catalog_dose) { 
				$current_amount = $catalog_dose;
				
				$current_physical_quantity = 1;
				while ($drug_dosage_requirements > $current_amount) {
					$current_amount += $catalog_dose;
					$current_physical_quantity += 1;
				}

				$current_amount = $current_amount - $drug_dosage_requirements;
			}
		} elseif (
			$package_id == 'd5df7392-0cc5-496b-a9ce-1c5b7426af3e' // VIAL
			|| $package_id == '2340f991-643c-4409-af29-7072b594d1ef' // AMPUL
		) {
			// Dosis dokter dalam bentuk MG
			if ($dose_unit_id == '61a257e9-2e4e-4e47-8387-0bc0fe90aeec') {
				// x = packaging_value * (catalog_dose - dose) / catalog_dose
				// x = catalog_dose (current_amount) / packaging_value
				
				// Menghitung kekuatan obat jika master kekuatan katalog dalam satuan MG/ML
				if ($catalog_dose_unit_id == 'f1b7bf36-1fc1-4786-90a4-d80b9cf2aaa8') { 
					$catalog_dose = $catalog_dose * $packaging_value;
				}

				$tmp_current_amount = $catalog_dose;
				$current_physical_quantity = 1;
				while ($drug_dosage_requirements > $tmp_current_amount) {
					$tmp_current_amount += $catalog_dose;
					$current_physical_quantity += 1;
				}

				/* 
					Ketika dosis dokter lebih dari satu kekuatan obat
					cth. katalog obat mengandung 125 MG
					dan dosis dari dokter 300 MG
				*/
				if ($dose > $catalog_dose) {
					$catalog_dose = $catalog_dose * $current_physical_quantity;
				}

				$current_amount = $packaging_value * ($catalog_dose - $dose) / $catalog_dose;
			} else {
				/* 
					Masuk kedalam block ini ketika dosis dokter dengan ML hasil dari
					convert 1 Ampul / 1 Vial ke volume kemasan
				*/

				$current_amount = $packaging_value;
				$current_physical_quantity = 1;
				while ($drug_dosage_requirements > $current_amount) {
					$current_amount += $packaging_value;
					$current_physical_quantity += 1;
				}

				$current_amount = $current_amount - $drug_dosage_requirements;
			}
		} else {
			$current_amount = $packaging_value - $drug_dosage_requirements;
			$current_physical_quantity = 1 * $times;
		}

		$current_amount = round($current_amount, 2);


		if ($registration_destination_id != null) {
			$select = 'a.id';
			$where = array(
				'a.registration_destination_id' => $registration_destination_id,
				'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
			);
			$kardex_stock = $this->Kardex_stock->select(0, $select, $where);
			if (count($kardex_stock) == 0) {
				$dt_kardex_stock = array(
					'registration_destination_id' => $registration_destination_id,
					'pharmacy_catalog_id' => $pharmacy_catalog_id,
					'signatura_times_id' => $signatura_times_id,
					'dose' => $dose,
					'dose_unit_id' => $dose_unit_id,
					'current_amount' => $current_amount,
					'current_amount_unit_id' => $packaging_unit_id,
					'current_physical_quantity' => $current_physical_quantity,
					'date_added' => date('Y-m-d H:i:s'),
					'date_updated' => date('Y-m-d H:i:s'),
				);
		
				$this->Kardex_stock->create($dt_kardex_stock);
			}
		}

		return $current_physical_quantity;
	}

	public function count_qty($pharmacy_catalog_id, $dose, $dose_unit_id, $signatura_times_id, $administer_id, $midnight_auto = false) {
		$select = 'a.id, a.package_id, a.dose, a.dose_unit_id, a.packaging_value, a.packaging_unit_id, p.name as package_name';
		$where = array();
		$join = array(
            array('package p', 'a.package_id = p.id', 'INNER'),
		);
		$catalog = $this->Catalog->select($pharmacy_catalog_id, $select, $where, $join);
		if (count($catalog) == 0) {
			return null;
		}

		// Define data master katalog
		$package_id = $catalog[0]->package_id;
		$package_name = $catalog[0]->package_name;
		$catalog_dose = $catalog[0]->dose;
		$catalog_dose_unit_id = $catalog[0]->dose_unit_id;
		$packaging_value = $catalog[0]->packaging_value;
		$packaging_unit_id = $catalog[0]->packaging_unit_id;

		// Balikan null jika master tidak lengkap
		if ($catalog_dose == null || $catalog_dose_unit_id == null || $packaging_value == null || $packaging_unit_id == null) {
			return 1;
		}

		// if (
		// 	$package_id == '7afbf19d-209c-407f-adbf-17ce9c82728d' // BOTOL
		// 	|| $dose_unit_id == 'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5' //	TABUR
		// 	|| $dose_unit_id == 'f4d6a5a3-cc89-46c8-895d-c31417b27a78' //	SPRAY
		// 	|| $dose_unit_id == '8ff1ce10-45cd-47fb-93bc-c6449946a099' //	TETES
		// 	|| $dose_unit_id == 'e72b6358-8d74-447c-b5be-9623a1903470' // OLES
		// 	|| $administer_id == '02d981b9-8644-479f-b5e1-465d695f066c' // Cara pakai dioleskan
		// 	|| ($package_id == 'c37c026c-f4e5-4f32-a4ff-1525bbe74803' && $administer_id == '02d981b9-8644-479f-b5e1-465d695f066c') // TUBE & Cara pakai dioleskan
		// ) { 
		// 	$current_physical_quantity = 1;
		// }
		// Default 1
		$current_physical_quantity = 1;
		$times = 1;

		/* 
			Mendapatkan epoch time untuk pagi, siang, sore, malam, pagi besok 

			Menghitung berapa banyak obat yang harus diberikan atau berapa banyak jumlah yang diminta
			ke Farmasi dengan cara dari waktu order resep

			cth. Diresepkan parasetamol 1 tablet, tiap 8 jam dan diorder pukul 08.00
			maka rumus yang akan digunakan 1 tablet * 3 (minum 14.00, 22.00, 06.00 (besok))
			hasil 3 tablet yang diminta ke Farmasi
		*/
		$now = time();
		$morning           = jakartaTimestamp('06:00:00');
		$after_morning     = jakartaTimestamp('10:00:00');
		$noon              = jakartaTimestamp('14:00:00');
		$afternoon         = jakartaTimestamp('18:00:00');
		$evening           = jakartaTimestamp('22:00:00');
		$after_evening     = jakartaTimestamp('02:00:00', 1);
		$tomorrow_morning  = jakartaTimestamp('06:00:00', 1);

		if (!$midnight_auto) {
			if ($now > $morning && $now < $noon) {
				if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
					if ($now < $after_morning) {
						$times = 6;
					} else {
						$times = 5;
					}
				}
				if ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
					$times = 4;
				} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
					$times = 3;
				} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
					$times = 1;
				} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
					$times = 1;
				}
				// echo 'pagi menjelang siang';
			} elseif ($now > $noon && $now < $afternoon) {
				if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
					$times = 4;
				} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
					$times = 3;
				} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
					$times = 2;
				} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
					$times = 1;
				} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
					$times = 1;
				}
				// echo 'siang menjelang sore';
			} elseif ($now > $afternoon && $now < $evening) {
				if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
					$times = 4;
				} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
					$times = 2;
				} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
					$times = 2;
				} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
					$times = 1;
				} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
					$times = 1;
				}
				// echo 'sore menjelang malam';
			} elseif ($now > $evening) {
				if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
					$times = 6;
				} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
					$times = 4;
				} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
					$times = 4;
				} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
					$times = 3;
				} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
					$times = 1;
				} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
					$times = 1;
				}
				// echo 'malam menjelang pagi';
			} elseif ($now < $morning) {
				if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
					$times = 5;
				} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
					$times = 4;
				} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
					$times = 3;
				} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
					$times = 2;
				} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
					$times = 1;
				} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
					$times = 1;
				}
			}
		} else {
			if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
				$times = 5;
			} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
				$times = 4;
			} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
				$times = 3;
			} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
				$times = 2;
			} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
            	$times = 2;
			} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
				$times = 1;
			} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
				$times = 1;
			} elseif ($signatura_times_id == 'ce7206d4-fb67-49d2-9a3a-3115a8c833f9') { // 1 JAM
				$times = 24;
			} elseif ($signatura_times_id == 'fc91b35a-9ac8-4009-bf43-531764dbeeec') { // 1 MENIT
				$times = 1440;
			}
		}

		if ($signatura_times_id == 'ce7206d4-fb67-49d2-9a3a-3115a8c833f9') { // 1 JAM
			// Current time
			$now = new DateTime();
			// Midnight (start of tomorrow)
			$midnight = new DateTime('tomorrow');
			// Difference
			$diff = $now->diff($midnight);
			// Get hours left (including minutes converted to fraction if you want)
			$hoursLeft = ceil($diff->h + ($diff->i / 60));
			$times = $hoursLeft;
			$times = $times + 10; // Ditambah 10 jam karena Farmasi mengantarkan obat jam 10
		}

		if ($signatura_times_id == 'fc91b35a-9ac8-4009-bf43-531764dbeeec') { // 1 MENIT
			// Current time
			$now = new DateTime();
			// Midnight (start of tomorrow)
			$midnight = new DateTime('tomorrow');
			// Difference in seconds
			$secondsLeft = $midnight->getTimestamp() - $now->getTimestamp();
			// Convert to minutes (round up to include partial minutes)
			$minutesLeft = ceil($secondsLeft / 60);
			$times = $minutesLeft;
			$times = $times + 600; // Ditambah 10 jam karena Farmasi mengantarkan obat jam 10
		}

		if (
			$dose_unit_id == '61a257e9-2e4e-4e47-8387-0bc0fe90aeec' // MG
			|| $dose_unit_id == 'ab328f03-5088-49e3-9f0d-c3ea95d0747d' // ML
			|| $dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02' // GR
			|| $dose_unit_id == '02e218ab-81d6-4217-82d1-46b686d272a5' // MCG
			|| $dose_unit_id == 'e1f18f3e-c563-4c51-a6b1-b529326f9447' // IU
		){
			$drug_dosage_requirements = $dose * $times; // Menghitung kebutuhan dosis selama seharian penuh
			
			if (
				$catalog_dose_unit_id == 'f1b7bf36-1fc1-4786-90a4-d80b9cf2aaa8' // MG/ML
				|| $catalog_dose_unit_id == '72fd98fc-5ae7-477b-ba69-32869e14546b' // MCG/ML
				|| $catalog_dose_unit_id == '713d0b84-21db-4e8f-a8f4-d7cfe81ef9d0' // GR/ML
				|| $catalog_dose_unit_id == 'dc3ac295-045a-4eb4-8f84-215b0e0696c5' // IU/ML
			) {
				$packaging_value = $catalog_dose * $packaging_value;

				if ($dose_unit_id == '02e218ab-81d6-4217-82d1-46b686d272a5') { // MCG
					$drug_dosage_requirements = $dose * 0.001 * $times; // MCG -> MG
				}

				if ($dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02') { // GR
					$drug_dosage_requirements = $dose * 1000 * $times; // GR -> MG
				}
			} elseif ($catalog_dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02' && $packaging_unit_id == 'ab328f03-5088-49e3-9f0d-c3ea95d0747d') { // GR AND ML
				if ($dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02') { // GR
					$drug_dosage_requirements = $dose * 1000 * $times; // GR -> MG
				}

				$packaging_value = $catalog_dose * 1000; // GR -> MG
			} elseif ($catalog_dose_unit_id == '61a257e9-2e4e-4e47-8387-0bc0fe90aeec' && $dose_unit_id == '61a257e9-2e4e-4e47-8387-0bc0fe90aeec') { // MG
				$packaging_value = $catalog_dose;
			}

			$current_physical_quantity = 1;
			$tmp_qty = $current_physical_quantity * $packaging_value;
			while ($drug_dosage_requirements > $tmp_qty) {
				$tmp_qty += $packaging_value;
				$current_physical_quantity += 1;
			}
		} elseif (
				$dose_unit_id == '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a' // TETES MIKRO
				|| $dose_unit_id == 'f68098fa-f6b4-4df2-9a4f-bc8298663574' // TETES MAKRO
			) {
			$drug_dosage_requirements = $dose * $times; // TETES

			if ($dose_unit_id == '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a') { // TETES MIKRO
				$drug_dosage_requirements = $drug_dosage_requirements / 60; // ML
			}

			if ($dose_unit_id == 'f68098fa-f6b4-4df2-9a4f-bc8298663574') { // TETES MAKRO
				$drug_dosage_requirements = $drug_dosage_requirements / 20; // ML
			}

			$current_physical_quantity = 1;
			$tmp_qty = $current_physical_quantity * $packaging_value;
			while ($drug_dosage_requirements > $tmp_qty) {
				$tmp_qty += $packaging_value;
				$current_physical_quantity += 1;
			}
		} elseif (
			$package_id == 'd5df7392-0cc5-496b-a9ce-1c5b7426af3e' // VIAL
			|| $package_id == '2340f991-643c-4409-af29-7072b594d1ef' // AMPUL
			|| $package_id == '7afbf19d-209c-407f-adbf-17ce9c82728d' // BOTOL
			|| $package_id == '357310dd-904f-4b24-92a2-7987a75cdfc5' // TABLET
			|| $package_id == 'c5bc8af2-c4eb-4d74-9566-39048ba2f00a' // KAPSUL
			|| $package_id == 'c14d6d95-4513-45dc-956d-a8da288e0a21' // OVULA
			|| $package_id == '54f6e791-283d-479d-ba82-9a728cc5ce7b' // SACHET
			|| $package_id == 'a0c282f5-6ee2-4577-b3c8-bf0779e515f5' // SUPP
			|| $package_id == '12b26d53-86c1-49dd-940c-acff164c423b' // SUPPOS
			|| $package_id == '7b430ad7-7462-47d3-bed7-7fb649369771' // SYRINGE
			|| $package_id == 'c37c026c-f4e5-4f32-a4ff-1525bbe74803' // TUBE
			|| $package_id == '140c8ede-9b7c-4fb6-bda7-512bc94df6bf' // PCS
			|| $package_id == 'e1fe968f-432e-4ef4-9815-14ebded83355' // BAG
		) {
			$current_physical_quantity = ceil($dose * $times);

			if ($dose_unit_id == 'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5' || $dose_unit_id == 'e72b6358-8d74-447c-b5be-9623a1903470' || $dose_unit_id == '8ff1ce10-45cd-47fb-93bc-c6449946a099') { // TABUR & OLES & TETES
				$current_physical_quantity = 1;
			}
		}

		return $current_physical_quantity;
	}

	public function count_amount($current_amount, $qty, $packaging_value) {
		return $qty * $packaging_value + $current_amount;
	}

	public function administer_patient($pharmacy_catalog_id, $current_amount, $current_amount_unit_id, $dose, $dose_unit_id, $catalog_dose, $catalog_dose_unit_id, $catalog_packaging_value, $catalog_packaging_unit_id) {
		$dose = clean_dose_unit($dose, $dose_unit_id, $pharmacy_catalog_id);
		$dose_unit_id = clean_unit($dose_unit_id);

		if ($dose_unit_id == 'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5' || $dose_unit_id == 'e72b6358-8d74-447c-b5be-9623a1903470' || $dose_unit_id == '8ff1ce10-45cd-47fb-93bc-c6449946a099') { // TABUR & OLES & TETES
			return [$current_amount, 1];
		}

		if ($dose_unit_id == $current_amount_unit_id) {
			$current_amount = $current_amount - $dose; // Pengurangan setelah 1x pemakaian
		} elseif (
				$dose_unit_id == '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a' // TETES MIKRO
				|| $dose_unit_id == 'f68098fa-f6b4-4df2-9a4f-bc8298663574' // TETES MAKRO
			) {
			if ($dose_unit_id == '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a') { // TETES MIKRO
				$dose = $dose / 60; // ML
			}
			if ($dose_unit_id == 'f68098fa-f6b4-4df2-9a4f-bc8298663574') { // TETES MAKRO
				$dose = $dose / 20; // ML
			}

			$current_amount = $current_amount - $dose; // Pengurangan setelah 1x pemakaian
		} else {
			if (
				$catalog_dose_unit_id == 'f1b7bf36-1fc1-4786-90a4-d80b9cf2aaa8' // MG/ML
				|| $catalog_dose_unit_id == '72fd98fc-5ae7-477b-ba69-32869e14546b' // MCG/ML
				|| $catalog_dose_unit_id == '713d0b84-21db-4e8f-a8f4-d7cfe81ef9d0' // GR/ML
				|| $catalog_dose_unit_id == 'dc3ac295-045a-4eb4-8f84-215b0e0696c5' // IU/ML
			) {
				if ($dose_unit_id == '02e218ab-81d6-4217-82d1-46b686d272a5') { // MCG
					$dose = $dose * 0.001; // MCG -> MG
				}

				if ($dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02') { // GR
					$dose = $dose * 1000;
				}
				$tmp_catalog_dose = $catalog_dose * $catalog_packaging_value; // MG
				$tmp_current_amount = $tmp_catalog_dose * $current_amount / $catalog_packaging_value; // ML -> MG
				$tmp_current_amount = $tmp_current_amount - $dose; // Pengurangan setelah 1x pemakaian
				$current_amount = $catalog_packaging_value * $tmp_current_amount / $tmp_catalog_dose; // MG -> ML
			} elseif ($catalog_dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02' && $catalog_packaging_unit_id == 'ab328f03-5088-49e3-9f0d-c3ea95d0747d') { // GR AND ML
				if ($dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02') { // GR
					$dose = $dose * 1000;
				}
				$tmp_catalog_dose = $catalog_dose * 1000; // GR -> MG
				$tmp_current_amount = $tmp_catalog_dose * $current_amount / $catalog_packaging_value; // ML -> MG
				$tmp_current_amount = $tmp_current_amount - $dose; // Pengurangan setelah 1x pemakaian
				$current_amount = $catalog_packaging_value * $tmp_current_amount / $tmp_catalog_dose; // MG -> ML
			} else {
				$current_amount = $current_amount - $catalog_packaging_value;
			}
		}

		$current_physical_quantity = 1;
		$tmp_catalog_packaging_value = $current_physical_quantity * $catalog_packaging_value;
		while ($tmp_catalog_packaging_value < $current_amount) {
			$tmp_catalog_packaging_value += $catalog_packaging_value;
			$current_physical_quantity += 1;
		}

		if ($current_amount <= 0) {
			$current_physical_quantity = 0;
		}

		return [$current_amount, $current_physical_quantity];
	}

	public function quantity_require($pharmacy_catalog_id, $signatura_times_id, $current_amount, $current_amount_unit_id, $dose, $dose_unit_id, $catalog_dose, $catalog_dose_unit_id, $catalog_packaging_value, $catalog_packaging_unit_id) {
		$dose = clean_dose_unit($dose, $dose_unit_id, $pharmacy_catalog_id);
		$dose_unit_id = clean_unit($dose_unit_id);

		if ($dose_unit_id == 'a6c14c74-d3c3-49ff-8ca9-45cc1682dbd5' || $dose_unit_id == 'e72b6358-8d74-447c-b5be-9623a1903470' || $dose_unit_id == '8ff1ce10-45cd-47fb-93bc-c6449946a099') { // TABUR & OLES & TETES
			return 1;
		}

		if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
			$times = 5;
		} elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
			$times = 4;
		} elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
			$times = 3;
		} elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
			$times = 2;
		} elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
			$times = 2;
		} elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
			$times = 1;
		} elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
            $times = 1;
        } elseif ($signatura_times_id == 'ce7206d4-fb67-49d2-9a3a-3115a8c833f9') { // 1 JAM
			$times = 24;
		} elseif ($signatura_times_id == 'fc91b35a-9ac8-4009-bf43-531764dbeeec') { // 1 MENIT
			$times = 1440;
		}

		$drug_dosage_requirements = $dose * $times;
		
		if ($dose_unit_id != $current_amount_unit_id) {
			if (
				$dose_unit_id == '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a' // TETES MIKRO
				|| $dose_unit_id == 'f68098fa-f6b4-4df2-9a4f-bc8298663574' // TETES MAKRO
			) {
				$drug_dosage_requirements = $dose * $times; // TETES

				if ($dose_unit_id == '5fd3d1f5-7e92-49b9-84a2-e6a01474d55a') { // TETES MIKRO
					$drug_dosage_requirements = $drug_dosage_requirements / 60; // ML
				}
				if ($dose_unit_id == 'f68098fa-f6b4-4df2-9a4f-bc8298663574') { // TETES MAKRO
					$drug_dosage_requirements = $drug_dosage_requirements / 20; // ML
				}
			} elseif (
				$catalog_dose_unit_id == 'f1b7bf36-1fc1-4786-90a4-d80b9cf2aaa8' // MG/ML
				|| $catalog_dose_unit_id == '72fd98fc-5ae7-477b-ba69-32869e14546b' // MCG/ML
				|| $catalog_dose_unit_id == '713d0b84-21db-4e8f-a8f4-d7cfe81ef9d0' // GR/ML
				|| $catalog_dose_unit_id == 'dc3ac295-045a-4eb4-8f84-215b0e0696c5' // IU/ML
			) {
				$tmp_catalog_dose = $catalog_dose * $catalog_packaging_value; // MG
				$drug_dosage_requirements = $catalog_packaging_value * $drug_dosage_requirements / $tmp_catalog_dose; // MG -> ML

				if ($dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02') { // GR
					$drug_dosage_requirements = $catalog_packaging_value * ($dose * 1000 * $times) / $tmp_catalog_dose; // GR -> ML
				}

				if ($dose_unit_id == '02e218ab-81d6-4217-82d1-46b686d272a5') { // MCG
					$drug_dosage_requirements = $catalog_packaging_value * ($dose * 0.001 * $times) / $tmp_catalog_dose; // MCG -> MG
				}
			} elseif ($catalog_dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02' && $catalog_packaging_unit_id == 'ab328f03-5088-49e3-9f0d-c3ea95d0747d') { // GR AND ML
				if ($dose_unit_id == '963fb7d8-9141-4f16-bcc7-727d0392ac02') {
					$drug_dosage_requirements = $dose * 1000 * $times;
				}
				$tmp_catalog_dose = $catalog_dose * 1000; // GR -> MG
				$drug_dosage_requirements = $catalog_packaging_value * $drug_dosage_requirements / $tmp_catalog_dose; // MG -> ML
			} else {
				$drug_dosage_requirements = $catalog_packaging_value * $times;
			}
		}

		$current_physical_quantity = 0;
		$tmp_current_amount = $current_amount;
		while ($tmp_current_amount < $drug_dosage_requirements) {
			$tmp_current_amount += $catalog_packaging_value;
			$current_physical_quantity += 1;
		}
		
		return $current_physical_quantity;
	}
}?>
