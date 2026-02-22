
<!-- Connect to the same CSS as the signup page so it looks consistent -->
<link rel="stylesheet" type="text/css" href="css/login_reg.css">
<link rel="stylesheet" type="text/css" href="css/utils.css">

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-l-50 p-r-50 p-t-72 p-b-50" style="width: 100%;">
            <form id="signup_form" onsubmit="return false" class="login100-form validate-form">
                <span class="login100-form-title p-b-59">
                    Sign Up
                </span>
                <div class="wrap-input100 validate-input" data-validate="Name is required">
                    <span class="label-input100">Full Name</span>
                    <input class="input100" type="text" name="f_name" id="f_name" placeholder="First Name">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Name is required">
                    <span class="label-input100">Last Name</span>
                    <input class="input100" type="text" name="l_name" id="l_name" placeholder="Last Name">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                    <span class="label-input100">Email</span>
                    <input class="input100" type="email" name="email"  placeholder="Email addess...">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Mobile no is required">
                    <span class="label-input100">Mobile</span>
                    <input class="input100" type="text" name="mobile" id="mobile" placeholder="mobile....">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Password is required">
                    <span class="label-input100">Password</span>
                    <input class="input100" type="password" name="password" id="password" placeholder="*************">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Repeat Password is required">
                    <span class="label-input100">Repeat Password</span>
                    <input class="input100" type="password" name="repassword" id="repassword" placeholder="*************">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Address is required">
                    <span class="label-input100">Address</span>
                    <input class="input100" type="text" name="address1" id="address1" placeholder="Address">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="City is required">
                    <span class="label-input100">City</span>
                    <input class="input100" type="text" name="address2" id="address2" placeholder="City">
                    <span class="focus-input100"></span>
                </div>
                
                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn" type="submit">
                            Sign Up
                        </button>
                    </div>
                </div>
                <div class="col-md-8" id="signup_msg"></div>
            </form>
        </div>
    </div>
</div>
