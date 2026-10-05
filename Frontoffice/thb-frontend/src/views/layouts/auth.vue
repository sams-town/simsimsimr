
<template>
    <div>

        <img class="wave" src="/img/login/frame_login.png">
        <div class="container">
            <div class="img">
                <img src="/img/login/login.png">
            </div>
            <div class="login-content">
                <form action="index.html">
                    <img src="/img/login/logo_zeemarr.png">
                    <p class="title1">Select the Product You Want</p>
                    <div class="row">
                        <div class='col text-center'>
                            <input type="radio" name="imgbackground" id="img1" class="d-none imgbgchk" value="">
                            <label for="img1">
                                <img src="/img/login/icon-his.png" alt="Image 1">
                                <div class="tick_container">
                                    <div class="tick"><i class="fa fa-check"></i></div>
                                </div>
                            </label>
                        </div>
                        <div class='col text-center'>
                            <input type="radio" name="imgbackground" id="img2" class="d-none imgbgchk" value="">
                            <label for="img2">
                                <img src="/img/login/icon-clinic.png" alt="Image 2">
                                <div class="tick_container">
                                    <div class="tick"><i class="fa fa-check"></i></div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <p class="title2">Select the Type of Product You Want</p>
                    <div class="row">
                        <div class="form-group">
                            <div class="radio inlineblock m-r-20">
                                <input type="radio" name="gender" id="male" class="with-gap" value="option1">
                                <label for="male">Holding</label>
                            </div>
                            <div class="radio inlineblock">
                                <input type="radio" name="gender" id="Female" class="with-gap" value="option2">
                                <label for="Female">Clinic</label>
                            </div>
                        </div>
                    </div>

                    <router-link to="/login2"><input type="submit" class="btn" value="Step 2 Profile Info">
                    </router-link>
                </form>
            </div>
        </div>
    </div>
</template>
<style>
* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    overflow: hidden;
}

.wave {
    position: fixed;
    bottom: 0;
    left: 0;
    height: 130%;
    z-index: -1;
}

.container {
    width: 100vw;
    height: 100vh;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-gap: 7rem;
    padding: 0 2rem;
}

.img {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.login-content {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    text-align: center;
    margin-bottom: 50px;
}

.img img {
    width: 500px;
}

form {
    width: 360px;
}

.login-content img {
    height: 100px;
}

.login-content h2 {
    margin: 15px 0;
    color: #333;
    text-transform: uppercase;
    font-size: 2.9rem;
}

.login-content .input-div {
    position: relative;
    display: grid;
    grid-template-columns: 7% 93%;
    margin: 25px 0;
    padding: 5px 0;
    border-bottom: 2px solid #d9d9d9;
}

.login-content .input-div.one {
    margin-top: 0;
}

.i {
    color: #d9d9d9;
    display: flex;
    justify-content: center;
    align-items: center;
}

.i i {
    transition: .3s;
}

.input-div>div {
    position: relative;
    height: 45px;
}

.input-div>div>h5 {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 18px;
    transition: .3s;
}

.input-div:before,
.input-div:after {
    content: '';
    position: absolute;
    bottom: -2px;
    width: 0%;
    height: 2px;
    background-color: #38d39f;
    transition: .4s;
}

.input-div:before {
    right: 50%;
}

.input-div:after {
    left: 50%;
}

.input-div.focus:before,
.input-div.focus:after {
    width: 50%;
}

.input-div.focus>div>h5 {
    top: -5px;
    font-size: 15px;
}

.input-div.focus>.i>i {
    color: #38d39f;
}

.input-div>div>input {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    border: none;
    outline: none;
    background: none;
    padding: 0.5rem 0.7rem;
    font-size: 1.2rem;
    color: #555;
    font-family: 'poppins', sans-serif;
}

.input-div.pass {
    margin-bottom: 4px;
}

a {
    display: block;
    text-align: right;
    text-decoration: none;
    color: #999;
    font-size: 0.9rem;
    transition: .3s;
}

a:hover {
    color: #38d39f;
}

.btn {
    /* Button */


    /* Auto layout */

    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 12px;
    gap: 10px;

    position: absolute;
    width: 149px;
    height: 39px;
    left: 1255px;
    top: 750px;

    background: #F58F64;
    border-radius: 10px;
}

.btn:hover {
    background-position: right;
}


@media screen and (max-width: 1050px) {
    .container {
        grid-gap: 5rem;
    }
}

@media screen and (max-width: 1000px) {
    form {
        width: 290px;
    }

    .login-content h2 {
        font-size: 2.4rem;
        margin: 8px 0;
    }

    .img img {
        width: 400px;
    }
}

.title1 {
    margin-top: 38px;
    margin-bottom: 24px;
}

.title2 {
    margin-top: 38px;
}

@media screen and (max-width: 900px) {
    .container {
        grid-template-columns: 1fr;
    }

    .img {
        display: none;
    }

    .wave {
        display: none;
    }

    .login-content {
        justify-content: center;
    }
}

/* checkbox */
.col img {
    height: 120px;
    background: #D758A9;
    border-radius: 10px;
    width: 110%;
    cursor: pointer;
    transition: transform 1s;
    object-fit: cover;
}

.col label {
    overflow: hidden;
    position: relative;
}

.imgbgchk:checked+label>.tick_container {
    opacity: 1;
}

/*         aNIMATION */
.imgbgchk:checked+label>img {
    transform: scale(1.25);
    opacity: 0.3;
}

.tick_container {
    transition: .5s ease;
    opacity: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    cursor: pointer;
    text-align: center;
}

.tick {
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    padding: 6px 12px;
    height: 40px;
    width: 40px;
    border-radius: 100%;
}

.img_email {
    width: 377.95px;
    height: 251.99px !important;
    left: 917px;
    top: 438px;

}

.btn4 {
    display: block;
    width: 100%;
    height: 50px;
    border-radius: 25px;
    outline: none;
    border: none;
    background: #F58F64;
    background-size: 200%;
    font-size: 1.2rem;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    text-transform: uppercase;
    margin: 1rem 0;
    cursor: pointer;
    transition: .5s;
}

.btn4:hover {
    background-position: right;
}
</style>