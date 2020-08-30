  
<?php if(isset($_SESSION['userEmail'])) { ?>
<form method="POST" action="/msgraph/User/block" class="needs-validation">
 <p class="h4 mb-4">Block User</p>
  <div class="form-row">
    <div class="col-md-4 mb-3">
      <label for="validationDefault01">User Mail</label>
      <input type="text" name="mail" class="form-control" id="validationDefault01" placeholder="john.test@multibankfx.com" required>
    </div>    
   </div>  
  
  <button class="btn btn-info" type="submit">Block User</button>
</form>
<?php } else { ?>
<p>To use this application you need to be logged in</p>
            <a href="/msgraph/Auth/sign" class="btn btn-info">Sign In</a>
<?php }?>