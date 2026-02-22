
<!-- Connect to the same CSS as the signup page so it looks consistent -->
<link rel="stylesheet" type="text/css" href="css/login_reg.css">
<link rel="stylesheet" type="text/css" href="css/utils.css">

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-l-50 p-r-50 p-t-72 p-b-50" style="width: 100%;">
            <form id="profile_form" onsubmit="return false" class="login100-form validate-form">
                <span class="login100-form-title p-b-59">
                    My Profile
                </span>
                
                <?php
                    // Fetch current user data
                    if(isset($_SESSION["uid"])){
                        $sql = "SELECT * FROM user_info WHERE user_id='$_SESSION[uid]'";
                        $query = mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($query);
                    }
                ?>

                <div class="wrap-input100 validate-input" data-validate="Name is required">
                    <span class="label-input100">First Name</span>
                    <input class="input100" type="text" name="f_name" id="f_name" value="<?php echo isset($row['first_name']) ? $row['first_name'] : ''; ?>">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Name is required">
                    <span class="label-input100">Last Name</span>
                    <input class="input100" type="text" name="l_name" id="l_name" value="<?php echo isset($row['last_name']) ? $row['last_name'] : ''; ?>">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                    <span class="label-input100">Email</span>
                    <input class="input100" type="email" name="email" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>" readonly>
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Mobile no is required">
                    <span class="label-input100">Mobile</span>
                    <input class="input100" type="text" name="mobile" id="mobile" value="<?php echo isset($row['mobile']) ? $row['mobile'] : ''; ?>">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Password empty means no change">
                    <span class="label-input100">New Password (leave empty to keep current)</span>
                    <input class="input100" type="password" name="password" id="password" placeholder="*************">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Address is required">
                    <span class="label-input100">Address</span>
                    <input class="input100" type="text" name="address1" id="address1" value="<?php echo isset($row['address1']) ? $row['address1'] : ''; ?>">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="City is required">
                    <span class="label-input100">City</span>
                    <input class="input100" type="text" name="address2" id="address2" value="<?php echo isset($row['address2']) ? $row['address2'] : ''; ?>">
                    <span class="focus-input100"></span>
                </div>
                
                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn" type="submit">
                            Update Profile
                        </button>
                    </div>
                </div>
                <div class="col-md-12" id="profile_msg"></div>
            </form>
        </div>
    </div>
</div>
