<template>
    <div>
        <div class="card">
            <div class="card-header bg-primary text-white">Abdomen</div>
            <div class="card-body bg-white d-flex">
                <div class="col-12 mx-auto px-0 px-lg-3">
                    <div class="bg-white rounded">
                        <div style="overflow: hidden; background-color: #eaebef;">
                            <div id="abdomen-screen" class="abdomen-input"></div>
                        </div>
                    </div>
                    <div class="mt-3 bg-white">
                        <div class="col p-2">
                            <div class="row mx-0">
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box my-auto color-picker" style="background-color: #0F0E14;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #EB5757;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #EC98B5;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #F2994A;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #6FCF97;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #27AE60;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #219653;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #00EDF5;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #8901EB;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #840083;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #FA01F8;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #990104;">
                                    </div>
                                </a>
                                <a class="btn col d-flex justify-content-center abdomen-color-picker" data-toggle="pill">
                                    <div class="rounded-circle box color-picker" style="background-color: #737278;">
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="row mx-0 p-2">
                            <div class="col-12 col-lg-6 px-0">
                                <div class="row mx-0">
                                    <a class="btn d-flex justify-content-center" id="abdomen-pointer-size-1">
                                        <img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
                                        <p class="mb-0">1</p>
                                    </a>
                                    <a class="btn d-flex justify-content-center" id="abdomen-pointer-size-2">
                                        <img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
                                        <p class="mb-0">2</p>
                                    </a>
                                    <a class="btn d-flex justify-content-center" id="abdomen-pointer-size-3">
                                        <img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
                                        <p class="mb-0">3</p>
                                    </a>
                                    <a class="btn d-flex justify-content-center" id="abdomen-pointer-size-4">
                                        <img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
                                        <p class="mb-0">4</p>
                                    </a>
                                    <a class=" btn d-flex justify-content-center" id="abdomen-pointer-size-5">
                                        <img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
                                        <p class="mb-0">5</p>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 mt-3 mt-lg-0 px-0">
                                <div class="d-flex">
                                    <div class="col-lg px-0 justify-content-lg-center d-flex">
                                        <a class="px-3 btn" id="abdomen-editor-brush" data-toggle="pill"><img
                                                src="@/@core/assets/icons/canvas/pencil.svg" alt="Pencil"></a>
                                        <a class="btn px-3 btn" id="abdomen-editor-text" data-toggle="pill"><img
                                                src="@/@core/assets/icons/canvas/text.svg" alt="Text"></a>
                                        <a class="btn px-3 btn" id="abdomen-editor-eraser" data-toggle="pill"><img
                                                src="@/@core/assets/icons/canvas/eraser.svg" alt="Eraser"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <b-button class="btn-danger col" id="abdomen-editor-clear">
                                    <img src="@/@core/assets/icons/canvas/trash.svg" width="20px" alt="trash-icon">Reset
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /end:card -->
    </div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szabdomencanvas.js'
export default {
    mounted() {
        // Canvas Abdomen
        var $abdomen = $('#abdomen-screen.abdomen-input').szAbdomenCanvas({
            responsive: true,
            canDraw: true,
        });
        $('#abdomen-pointer-size-1').click(function (e) {
            e.preventDefault();
            $abdomen.szAbdomenCanvas('setPointSize', 2);
        });

        $('#abdomen-pointer-size-2').click(function () {
            $abdomen.szAbdomenCanvas('setPointSize', 5);
        });

        $('#abdomen-pointer-size-3').click(function () {
            $abdomen.szAbdomenCanvas('setPointSize', 10);
        });

        $('#abdomen-pointer-size-4').click(function () {
            $abdomen.szAbdomenCanvas('setPointSize', 15);
        });

        $('#abdomen-pointer-size-5').click(function () {
            $abdomen.szAbdomenCanvas('setPointSize', 20);
        });

        $('#abdomen-editor-brush').click(function () {
            $abdomen.szAbdomenCanvas('setMode', 'brush');
        });

        $('#abdomen-editor-text').click(function () {
            $abdomen.szAbdomenCanvas('setMode', 'text');
        });

        $('#abdomen-editor-eraser').click(function () {
            $abdomen.szAbdomenCanvas('setMode', 'eraser');
        });

        $('.abdomen-color-picker').click(function (e) {
            e.preventDefault();
            var color = $(this).find('.color-picker').css('background-color');
            $abdomen.szAbdomenCanvas('setColor', color);
            $('#canvas-screen-current-color').css('background-color', color);
        });

        $('#abdomen-editor-clear').click(function () {
            $abdomen.szAbdomenCanvas('clearCanvas');
            confirm('Hapus semua gambar Abdomen?');
            return false;
        });
    },
}
</script>

<style scoped>
canvas {
    border: 0;
    overflow: hidden;
    width: 80%;
}

a.btn {
    background-color: whitesmoke;
    color: black;
}
a.btn:hover {
    background-color: #efdffc;
    color: #0F0E14;
}
a.btn:active {
    background-color: #d3a7ff;
    color: #0F0E14;
}

.box {
    width: 25px;
    height: 25px;
    aspect-ratio: 1/1;
}
</style>