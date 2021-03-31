<?php 
session_start();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Examples and usage guidelines for form control styles, layout options, and custom components for creating a wide variety of forms.">
<meta name="author" content="ootstrap contributors">
<meta name="generator" content="">
<title>Registration</title>

<!-- Bootstrap core CSS -->
<link href="css/bootstrap.min.css" rel="stylesheet" >
<!-- Documentation extras -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<style>
  .justify-content-center 
  {
    -ms-flex-pack: center !important;
    justify-content: center !important;
  }
  .required {
  color: red;
}

.p-t-10 {

  padding-top: 10px;
}

.m-t-10 {

  margin-top: 10px;
}
</style>


<div class="container">
    <div class="row">
          <div class="col-xs-8 justify-content-center p-t-10 m-t-10">
          <form>
            <div class="form-group">
              <label for="exampleFormControlInput1">First Name <span aria-hidden="true" class="required">*</span></label>
              <input type="text" class="form-control" name="fname" id="fname" placeholder="First Name">
              
            </div>

            <div class="form-group">
              <label for="exampleFormControlInput1">Last Name <span aria-hidden="true" class="required">*</span></label>
             <input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name">    
            </div>

             <div class="form-group">
              <label for="exampleFormControlInput1">Phone Number <span aria-hidden="true" class="required">*</span></label>
             <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" maxlength="10">    
            </div>

             <div class="form-group">
              <label for="exampleFormControlInput1">Email <span aria-hidden="true" class="required">*</span></label>
             <input type="text" class="form-control" name="email" id="email" placeholder="Email" onblur="check_exists_email();">    
            </div>
            
             <div class="form-group">
              <label for="exampleFormControlInput1"></label>
              <div id="error" class="alert alert-danger hide"></div>    
            </div>

             <div class="form-group">
              <label for="exampleFormControlInput1"></label>
              <div id="success" class="alert alert-success hide"></div>    
            </div>
            
            <div class="form-group">
              <label for="exampleFormControlFile1"></label>
              <button type="button" class="btn btn-primary" onclick="return user_add();">Submit</button>
            </div>

          </form>
          </div>
</div>
      
      <!-- Users records -->
      <div class="row"> 
            <div id="user_dataset">
            </div>
      </div>
</div>
<script>

//validation code start here 
$("#phone").keypress(function (e) {
   //if the letter is not digit then display error and don't type anything
   if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {        
      
      return false;
  }
 });

 $( "#fname, #lname" ).keypress(function(e) {
    var key = e.keyCode;
    if (key >= 48 && key <= 57) {
        e.preventDefault();
    }
});

function validateEmail($email) {
   var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
} 

 //validation code end here 

 function user_add()
 {
    var email = $("#email").val();

    if($.trim($("#fname").val())=="")
    {
       $("#error").removeClass("hide");
       $("#error").html("Please provide first name.");

       setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
       return false;
    } 

    else if($.trim($("#lname").val())=="")
    {
       $("#error").removeClass("hide");
       $("#error").html("Please provide last name.");

       setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
       return false;
    }

    else if($.trim($("#phone").val())=="")
    {
       $("#error").removeClass("hide");
       $("#error").html("Please provide phone.");

       setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
       return false;
    }
    
    else if( !validateEmail(email)) {

      $("#error").removeClass("hide");
      $("#error").html("Please provide valid email address.");
      setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
      return false;

    }

    else if($.trim(email)=="")
    {
       $("#error").removeClass("hide");
       $("#error").html("Please provide email address.");

       setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
       return false;
    } 

    else if(!check_exists_email() && typeof(check_exists_email())!='undefined')
    {
       
       $("#error").removeClass("hide");
       $("#error").html("Email already exists.");
       setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
       return false;
    }   

    
    else {
      
      $("#error").addClass("hide");

      //read the data from the form 
      var form_data = new FormData();
      form_data.append('fname',$('#fname').val());
      form_data.append('lname',$('#lname').val());      
      form_data.append('phone',$('#phone').val());
      form_data.append('email',$('#email').val());

      // AJAX request
     $.ajax({
       url: 'ajax_add_data.php?action=insert_auth', 
       type: 'post',
       data: form_data,
       dataType: 'json',
       contentType: false,
       processData: false,
       success: function (response) {        

          console.log(response);

          if(response=='101')  // record added successfully and post data using auth successful
          {
             $("#success").removeClass("hide");
             $("#success").html("Record added and posted successfully.");

             setTimeout(function(){ $("#success").html("");$("#success").addClass("hide"); user_get_data();}, 3000);
          }

          else if(response=='100')  // curl Connection failed
          {
             $("#error").removeClass("hide");
             $("#error").html("Connection Failed while posting data...");
             setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
          }

          else if(response=='102')  //Authentication Failed..
          {
             $("#error").removeClass("hide");
             $("#error").html("Authentication Failed.");
             setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
          }           
       }
     });

 } 
 }

 //get the posted data using Oauth 
 function user_get_data()
 {

  $.ajax({
       url: 'ajax_add_data.php?action=get_data', 
       type: 'get',
       
       dataType: 'json',
       contentType: false,
       processData: false,
       success: function (response) {    

          //display the users data onto page 
          $("#user_dataset").html(response);
      }
  });


}

//checked exists email into db
function check_exists_email()
{
  var form_data = new FormData();
  form_data.append('email',$('#email').val());
  
 var exists=1;
   $.ajax({
       url: 'ajax_add_data.php?action=check_exists_email', 
       type: 'post',
       data: form_data,
       dataType: 'json',
       contentType: false,
       processData: false,
       success: function (response) {    

          //display the users data onto page 
          if(response=='1')  //email exists
          {
              exists=0;
              $("#error").removeClass("hide");
              $("#error").html("Email already exists.");
              setTimeout(function(){ $("#error").html("");$("#error").addClass("hide"); }, 3000);
              return exists;
          }else {
             exists=1;
             return exists;
          }
          
      }
  });
   //return exists;
}


$( document ).ready(function() {
    user_get_data();//on page load also we can get the data if token already set 
});
  

</script>