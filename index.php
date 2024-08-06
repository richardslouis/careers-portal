<?php 
// Pear library includes
// You should have the pear lib installed
include_once('Mail.php');
include_once('Mail/mime.php');

//Settings 
$max_allowed_file_size = 5000; // size in KB 
$allowed_extensions = array("jpg", "jpeg", "png", "pdf", "docx", "doc", ".txt", ".rtf", ".odt", ".wpd");
$upload_folder = 'uploads/'; //<-- this folder must be writeable by the script
$your_email = 'careers@richiesoft.support';//<<--  update this to your email address

$errors ='';

if(isset($_POST['submit']))
{
	//Get the uploaded file information
	$name_of_uploaded_file =  basename($_FILES['uploaded_file']['name']);
	
	//get the file extension of the file
	$type_of_uploaded_file = substr($name_of_uploaded_file, 
							strrpos($name_of_uploaded_file, '.') + 1);
	
	$size_of_uploaded_file = $_FILES["uploaded_file"]["size"]/1024;
	
	///------------Do Validations-------------
	if(empty($_POST['firstname'])||empty($_POST['email']))
	{
		$errors .= "\n First Name and Email are required fields. ";	
	}
	if(IsInjected($visitor_email))
	{
		$errors .= "\n Bad email value!";
	}
	
	if($size_of_uploaded_file > $max_allowed_file_size ) 
	{
		$errors .= "\n Size of file should be less than $max_allowed_file_size";
	}
	
	//------ Validate the file extension -----
	$allowed_ext = false;
	for($i=0; $i<sizeof($allowed_extensions); $i++) 
	{ 
		if(strcasecmp($allowed_extensions[$i],$type_of_uploaded_file) == 0)
		{
			$allowed_ext = true;		
		}
	}
	
	if(!$allowed_ext)
	{
		$errors .= "\n The uploaded file is not supported file type. ".
		" Only the following file types are supported: ".implode(',',$allowed_extensions);
	}
	
	//send the email 
	if(empty($errors))
	{
		//copy the temp. uploaded file to uploads folder
		$path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
		$tmp_path = $_FILES["uploaded_file"]["tmp_name"];
		
		if(is_uploaded_file($tmp_path))
		{
		    if(!copy($tmp_path,$path_of_uploaded_file))
		    {
		    	$errors .= '\n error while copying the uploaded file';
		    }
		}
		
		//send the email
		$job_role = $_POST['role'];
		$first_name = $_POST['firstname'];
		$last_name = $_POST['lastname'];
		$visitor_email = $_POST['email'];
		$phone = $_POST['phone'];
		$location = $_POST['location'];
		$linkedin_profile = $_POST['linkedin'];
		$website = $_POST['website'];
		$user_message = $_POST['message'];
		$to = $your_email;
		$subject="$first_name $last_name | $job_role | $location";
		$from = $visitor_email;
		$text = "Role/Profile: $job_role \n| Location: $location \n\n\nName : $first_name $last_name \n\nLinkedin : $linkedin_profile \nWebsite : $website \nEmail | Phone : $visitor_email | $phone \n\nCover Note : $user_message";
		
		$message = new Mail_mime(); 
		$message->setTXTBody($text); 
		$message->addAttachment($path_of_uploaded_file);
		$body = $message->get();
		$extraheaders = array("From"=>$from, "Subject"=>$subject,"Reply-To"=>$from);
		$headers = $message->headers($extraheaders);
		$mail = Mail::factory("mail");
		$mail->send($to, $headers, $body);
		//redirect to 'thank-you page
		ob_start();
		header('Location: success.html');
		ob_end_flush();
	}
}
///////////////////////////Functions/////////////////
// Function to validate against any email injection attempts
function IsInjected($str)
{
  $injections = array('(\n+)',
              '(\r+)',
              '(\t+)',
              '(%0A+)',
              '(%0D+)',
              '(%08+)',
              '(%09+)'
              );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
    {
    return true;
  }
  else
    {
    return false;
  }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Careers | Richiesoft</title>

    <!-- Styles -->
    <link href="assets/css/core.min.css" rel="stylesheet">
    <link href="assets/css/thesaas.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="icon" href="assets/img/favicon.ico">
	  
	<!-- Form scripts -->
	<script language="JavaScript" src="scripts/gen_validatorv31.js" type="text/javascript"></script>
  </head>

  <body>


    <!-- Topbar -->
    <nav class="topbar topbar-inverse topbar-expand-sm">
      <div class="container">
        
        <div class="topbar-left">
          <a class="topbar-brand" href="index.html">
            <img class="logo-default" src="assets/img/richiesoftlogo.png" alt="Richiesoft">
            <img class="logo-inverse" src="assets/img/richiesoftlight.png" height="80px" alt="Richiesoft">
          </a>
        </div>

        <div class="topbar-right">
          <a class="btn btn-sm btn-outline btn-white hidden-md-up" href="http://contact.richiesoft.org"><i class="fa fa-address-card-o fa-lg"></i></a>
	      <a class="btn btn-sm btn-outline btn-white hidden-sm-down" href="http://contact.richiesoft.org">Contact</a>
          <a class="btn btn-sm btn-outline btn-white hidden-sm-down" href="http://richiesoft.org" target="_blank">Richiesoft Group</a>
        </div>


      </div>
    </nav>
    <!-- END Topbar -->



    <!-- Header -->
    <header class="header header-inverse h-fullscreen pb-80" style="background-image: url(assets/img/bg-richiesoft.jpg)" data-overlay="5">
      <div class="container text-center">

        <div class="row">
          <div class="col-12 col-lg-8 offset-lg-2">

            <h1 class="display-4 hidden-sm-down">Get A Job<br><span class="text-primary" data-type="at New York City, at Chennai, at New Delhi, Remotely, at Bengaluru , at Paris"></span><br><br></h1>
			<h1 class="hidden-md-up"><br>Get A Job<br><span class="text-primary" data-type="at New York City, at Chennai, at New Delhi, Remotely, at Bengaluru , at Paris"></span></h1>
            <p class="hidden-md-up"><br>You have got the chance to work and thrive with us. We at Richiesoft | ILRLabs | Doifoo want to make a family!</p>
			<p class="lead text-white fs-20 hidden-sm-down">You have got the chance to work and thrive with us.<br>We at Richiesoft | ILRLabs | Doifoo want to make a family!</p>

            <br>
            <hr class="w-60">
            

            <a class="btn btn-lg btn-round btn-white btn-outline w-200" href="#" data-scrollto="section-open-positions">Open Positions</a>
			<a class="btn btn-lg btn-round w-200 btn-white btn-outline hidden-sm-down" href="#" data-scrollto="section-apply">Submit Resume</a>

          </div>
        </div>

      </div>
    </header>
    <!-- END Header -->




    <!-- Main container -->
    <main class="main-content">


      <!--
      |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
      | Open positions
      |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
      !-->
      <section class="section" id="section-open-positions">
        <div class="container">
          <header class="section-header">

            <p class="lead hidden-md-up">Following list displays our current required positions.<br><br>
			<u>Other variants</u><br><a href="http://careers.denebsky.com">Denebsky</a> | <a href="http://primelancers.com">Primerlancers</a></p>
			<p class="lead hidden-sm-down">Following list displays our current required positions. This list will update regularly.<br>
			<u>Choose other variants</u> : <a href="http://careers.denebsky.com">Denebsky</a> (A fin-tech startup) | <a href="http://primelancers.com">Primelancers</a> (Part-time/Interns)</p>
          </header>


          <div class="accordion" id="accordion-job">

            <div class="card">
              <h5 class="card-title">
                <a class="d-flex" data-toggle="collapse" data-parent="#accordion-job" href="#collapse-job-1">
                  <span class="mr-auto">Senior Analyst, Doifoo (#00762)</span>
                  <span class="text-lighter hidden-sm-down"><i class="fa fa-map-marker mr-8"></i> New York City</span>
                </a>
              </h5>

              <div id="collapse-job-1" class="collapse in">
                <div class="card-block">
                  <p> Responsibilities<br>
-Take ownership of product user experience and design the interactions and interfaces, based upon the market and user acceptance<br>
-Researching and articulating user’s needs to design interfaces from the perspective of target audience<br>
-Wireframing, prototyping for a complete responsive design - mobile, desktop and accessories<br>
-Document design rules and design pixel perfect mock-ups<br></p>

<p>Should have<br>
-Strong conceptualization and strong visual communication ability<br>
-Exceptional design skills, production value and attention to detail<br>
-Working knowledge of design tools<br>
-Acquainted with the latest design trends and practices<br>
-Basic knowledge of HTML/CSS would be a plus<br></p>
 
				  <hr class="w-100">

                  <p class="text-center"><a class="btn btn-lg btn-primary" href="#section-apply">Apply Now</a></p>
                </div>
              </div>
            </div>


            <div class="card">
              <h5 class="card-title">
                <a class="d-flex" data-toggle="collapse" data-parent="#accordion-job" href="#collapse-job-2">
                  <span class="mr-auto">Front-end Engineer, ILRLabs (#00345)</span>
                  <span class="text-lighter hidden-sm-down"><i class="fa fa-map-marker mr-8"></i> Remotely</span>
                </a>
              </h5>

              <div id="collapse-job-2" class="collapse">
                <div class="card-block">
                  <p>As a front-end engineer you will be responsible for implementing complex user interactions for all of our web-based product interfaces. You will be working directly with our world-class designers and product managers to build a product that is beautiful, powerful and a pleasure to use. <br></p>
<p>-Developing new user-facing features in Progressive Web App. Integrating back-end API s with front-end. <br>
-Build reusable code and libraries for future use.<br>
-Ensure the technical feasibility of UI/UX designs.<br>
-Optimize applications for maximum speed and scalability.<br> 
-Assure that all user input is validated before submitting to back-end services<br>
-Collaborate with other team members and stakeholders.<br>
-Good communication skills are a must.<br>
- Ability to deal with uncertainty<br>
- Doggedness and determination<br>
- Should not have problems in shifting technologies, we are mainly looking for someone who’s very dynamic when it comes to programming languages.<br>
</p>

                  <hr class="w-100">

                  <p class="text-center"><a class="btn btn-lg btn-primary" href="#section-apply">Apply Now</a></p>
                </div>
              </div>
            </div>


            <div class="card">
              <h5 class="card-title">
                <a class="d-flex" data-toggle="collapse" data-parent="#accordion-job" href="#collapse-job-3">
                  <span class="mr-auto">BDE, Richiesoft (#00291)</span>
                  <span class="text-lighter hidden-sm-down"><i class="fa fa-map-marker mr-8"></i>New Delhi, India</span>
                </a>
              </h5>

              <div id="collapse-job-3" class="collapse">
                <div class="card-block">
                  <p>-To sell the entire product range to specific clientele<br>
-Exploring business opportunities by identifying prospects and evaluating their position in the industry; researching and analyzing sales options.<br>
-Establishing contacts and developing relationships with prospects<br>
-Establish and maintain relationships with clients and provide them ideas, information and solutions.<br>
-Handling clients for the agency and coordinate with design team<br>
-Motivation for Sale<br>
-Excellent interpersonal skills; ability to develop strong client relationships with senior and top level management<br>
-Thrives on working within a dynamic and highly collaborative team environment<br>
-Should have good contacts in companies especially with Communications & Brand Managers.<br>
-Target oriented and result driven<br>
</p>

                  <hr class="w-100">

                  <p class="text-center"><a class="btn btn-lg btn-primary" href="#section-apply">Apply Now</a></p>
                </div>
              </div>
            </div>


            <div class="card">
              <h5 class="card-title">
                <a class="d-flex" data-toggle="collapse" data-parent="#accordion-job" href="#collapse-job-4">
                  <span class="mr-auto">Media Buying, Richiesoft (#00236)</span>
                  <span class="text-lighter hidden-sm-down"><i class="fa fa-map-marker mr-8"></i>New Delhi, India</span>
                </a>
              </h5>

              <div id="collapse-job-4" class="collapse">
                <div class="card-block">
                  <p>-Selling Advertisement Space in Business, Fashion and Technology genre to Corporates.<br>
-Perform Cold calling to retain existing clients.<br>
-Visit Corporate to inquire about advertising sales<br>
-Focus on Client Acquisition.<br>
-Present sample ads to Clients.<br>
-Attending conferences and Exhibitions.<br>

</p>

                  <hr class="w-100">

                  <p class="text-center"><a class="btn btn-lg btn-primary" href="#section-apply">Apply Now</a></p>
                </div>
              </div>
            </div>


            <div class="card">
              <h5 class="card-title">
                <a class="d-flex" data-toggle="collapse" data-parent="#accordion-job" href="#collapse-job-5">
                  <span class="mr-auto">Content Writer, Richiesoft (#00192)</span>
                  <span class="text-lighter hidden-sm-down"><i class="fa fa-map-marker mr-8"></i> Chennai, India</span>
                </a>
              </h5>

              <div id="collapse-job-5" class="collapse">
                <div class="card-block">
                  <p>Richiesoft is looking for a Content Writer with experience in Technology, Fashion & Lifestyle and the Business industry.</p>
<p>-Content updation and new content creation for magazines<br>
-Write press releases, articles and case studies<br>
-Create content for email marketing, newsletter campaign, forums posting, blogs commenting<br>
-Participate in group discussion and moderate the group activities<br>
-Create blog posts about product updates and new features<br>
-Create presentations for sales and training<br>
-Managing and auditing the web content online<br>
-Research current market trends and make recommendations<br>
-Manage company's social media initiatives (Facebook, Xing, Myspace, Twitter, Linkedin, etc)<br>
-Should write well<br>
-Multitask-er<br>
-Familiarity with XML and HTML, Microsoft Office Products, including styles and templates<br>
-Project Coordination: Creating, Launching, executing and monitoring online campaigns<br>
</p>

                  <hr class="w-100">

                  <p class="text-center"><a class="btn btn-lg btn-primary" href="#section-apply">Apply Now</a></p>
                </div>
              </div>
            </div>

          </div>


        </div>
      </section>


      <!--
      |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
      | Apply form
      |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
      !-->
      <section class="section" id="section-apply">
        <div class="container">
          <header class="section-header">
            <small>Submit your resume</small>
            <h2>Apply to Richiesoft | ILRLabs | Doifoo</h2>
            <hr>
            <p class="lead">Prepare your resume and fill the following form. We will contact you as soon as possible.</p>
          </header>


          <div class="row">
            <div class="col-12 col-md-8 offset-md-2" id="apply">
              
              <?php
			if(!empty($errors))
				{
					echo nl2br($errors);
					}
				?>
			<form method="POST" name="email_form_with_php" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
			  
				<div class="form-group">
                  <select class="form-control" label="Select Job role" name="role">
					<option value="NA">--SELECT JOB ROLE/ SUBMIT TYPE--</option>
					<option value="RESUME SUBMIT">UPLOADING MY RESUME | NON-JOB SPECIFIC</option>
					<option value="Senior Analyst, Doifoo">Senior Analyst, Doifoo (#00762)</option>
					<option value="Front-end Engineer, ILRLabs">Front-end Engineer, ILRLabs (#00345)</option>
					<option value="BDE, Richiesoft">BDE, Richiesoft (#00291)</option>
					<option value="Media Buying, Richiesoft">Media Buying, Richiesoft (#00236)</option>
					<option value="Content Writer, Richiesoft">Content Writer, Richiesoft  (#00192)</option>
				  </select>
                </div>

                <div class="row">
                  <div class="form-group col-12 col-md-6">
				   <input class="form-control" type="text" placeholder="First Name" name="firstname">
                  </div>

                  <div class="form-group col-12 col-md-6">
                    <input class="form-control" type="text" placeholder="Last Name" name="lastname">
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-12 col-md-6">
                    <input class="form-control" type="text" placeholder="Email" name="email">
                  </div>

                  <div class="form-group col-12 col-md-6">
                    <input class="form-control" type="text" placeholder="Phone" name="phone">
                  </div>
                </div>

                <div class="form-group">
                  <input class="form-control" type="text" placeholder="Location" name="location">
                </div>

                <div class="form-group">
                  <input class="form-control" type="text" placeholder="Linkedin Profile" name="linkedin">
                </div>

                <div class="form-group">
                  <input class="form-control" type="text" placeholder="Website" name="website">
                </div>

                <div class="form-group input-group file-group">
                  
                  <input type="file" placeholder="Choose file..." name="uploaded_file">
                  <span class="input-group-btn">
                    <button class="btn btn-white file-browser" type="button"><i class="fa fa-upload"></i></button>
                  </span>
                </div>

                <div class="form-group">
                  <textarea class="form-control" placeholder="Extra information" rows="3" name="message"></textarea>
                </div>

                <button class="btn btn-primary btn-block" type="submit" value="Submit" name="submit">Submit your resume</button>
              </form>
				
				<script language="JavaScript">
// Code for validating the form
// Visit http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
// for details
var frmvalidator  = new Validator("email_form_with_php");
frmvalidator.addValidation("firstname","req","Please provide your name"); 
frmvalidator.addValidation("email","req","Please provide your email"); 
frmvalidator.addValidation("email","email","Please enter a valid email address"); 
</script>

            </div>
          </div>


        </div>
      </section>




    </main>
    <!-- END Main container -->






    <!-- Footer -->
    <footer class="site-footer">
      <div class="container">
        <div class="row gap-y align-items-center">
          <div class="col-12 col-lg-3">
            <p class="text-center text-lg-left">
              <a href="index.html"><img src="assets/img/richiesoftlogo.png" height="40px" alt="logo"></a>
            </p>
          </div>

          <div class="col-12 col-lg-6">
            <ul class="nav nav-inline nav-primary nav-hero">
              <li class="nav-item">
                <a class="nav-link" href="http://www.richiesoft.com">Richiesoft</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="http://www.ilrlabs.com">ILRLabs</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="http://www.doifoo.com">Doifoo</a>
              </li>
              <li class="nav-item hidden-sm-down">
                <a class="nav-link" href="http://richiesoft.org/terms.html" target="_blank">Terms</a>
              </li>
           </ul>
			<p align="center">&copy; 2013-21 | Made with <span style="font-size:200%;color:red;">&hearts;</span> at Chennai, India</p>
          </div>

          <div class="col-12 col-lg-3">
            <div class="social text-center text-lg-right">
              <a class="social-facebook" href="https://www.facebook.com/richiesoftinc" target="_blank"><i class="fa fa-facebook"></i></a>
              <a class="social-twitter" href="https://twitter.com/richiesoftinc" target="_blank"><i class="fa fa-twitter"></i></a>
              <a class="social-linkedin" href="https://www.linkedin.com/company/richiesoft-inc" target="_blank"><i class="fa fa-linkedin"></i></a>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->



    <!-- Scripts -->
    <script src="assets/js/core.min.js"></script>
    <script src="assets/js/thesaas.min.js"></script>
    <script src="assets/js/script.js"></script>

  </body>
</html>
