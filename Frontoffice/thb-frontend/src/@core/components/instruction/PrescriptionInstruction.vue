<template>
    <div>
        <div v-if="ins_section == 'signatura'" class="fs-10">
            <div>
                <div>untuk <code>OBAT SATUAN</code>, gunakan format berikut:</div>
                <div>
                    <code class="bg-success-100 text-success-700 p-1">[Signatura]</code>
                </div>
                <div class="mt-1">
                    <span class="mr-1">contoh:</span>
                    <span class="mr-1">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        1x1
                    </span>
                    <span class="mr-1">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        1x2
                    </span>
                    <span>
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        1 AMPUL/ 24 JAM
                    </span>
                </div>
            </div>
            <div class="mt-3">
                <div>untuk <code>RACIKAN</code>, gunakan format berikut:</div>
                <div>
                    <code class="bg-success-100 text-success-700 p-1 mr-1">[Signatura]</code>
                    <code class="bg-warning-100 text-warning-700 p-1 mr-1">[Waktu Penggunaan]</code>
                    <code class="bg-info-100 text-info-700 p-1 mr-1">[Aturan Pakai]</code>
                </div>
                <div class="mt-1">
                    <div class="mr-1">contoh:</div>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        1x1 siang setelah makan
                    </li>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        1 OLES/ 24 JAM Pagi Pemakaian luar
                    </li>
                </div>
            </div>
        </div>

        <div v-if="ins_section == 'notes'" class="fs-10">
            <div>
                <div>untuk <code>OBAT SATUAN</code>, gunakan format berikut:</div>
                <div>
                    <code class="bg-warning-100 text-warning-700 p-1 mr-1">[Waktu Penggunaan]</code>
                    <code class="bg-info-100 text-info-700 p-1 mr-1">[Aturan Pakai]</code>
                </div>
                <div class="mt-1">
                    <div class="mr-1">contoh:</div>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        siang setelah makan
                    </li>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        pagi-malam Pemakaian luar
                    </li>
                </div>
            </div>
            <div class="mt-3">
                <div>untuk <code>RACIKAN</code>, Berikan instruksi rinci untuk apoteker mengenai cara penanganan masing-masing obat saat proses peracikan</div>
                <div class="mt-1">
                    <div>Contoh:</div>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        Larutkan dalam 50ml air steril
                    </li>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        Ambil 75mg dari total kandungan
                    </li>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        Tambahkan setelah base dingin
                    </li>
                    <li style="list-style-type: none;">
                        <feather-icon icon="CheckIcon" size="10" color="green" />
                        Hindari kontak dengan logam
                    </li>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PrescInstruction',
    props: {
        ins_section: {
            type: String,
            default: ''
        },
    }
}
</script>