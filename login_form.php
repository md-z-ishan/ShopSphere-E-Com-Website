
<!-- Connect to the same CSS as the signin page so it looks consistent -->
<link rel="stylesheet" type="text/css" href="css/login_reg.css">
<link rel="stylesheet" type="text/css" href="css/utils.css">

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-l-50 p-r-50 p-t-72 p-b-50" style="width: 100%;">
            <form onsubmit="return false" id="login" class="login100-form validate-form">
                <span class="login100-form-title p-b-59">
                    Login
                </span>
                <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                    <span class="label-input100">Email</span>
                    <input class="input100" type="email" name="email" placeholder="Email addess...">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Password is required">
                    <span class="label-input100">Password</span>
                    <input class="input100" type="password" name="password" placeholder="*************">
                    <span class="focus-input100"></span>
                </div>
                <!-- REMOVED "Remember me" checkbox to keep it simple for modal -->
                
                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn" type="submit">
                            Sign in
                        </button>
                    </div>
                </div>
                <div class="alert alert-danger"><h4 id="e_msg"></h4></div>
            </form>
        </div>
    </div>
</div>
