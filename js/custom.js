var $n = jQuery.noConflict();  
$n(document).ready(function() {
		$n("#addimage").validate({
				rules: {
					imagetitle: {
						required:true, 
						maxlength: 200
					},imageurl: {
						url:true,  
						maxlength: 500
					},
					image_name:{
						isimage:true  
					}
				},
				errorClass: "image_error",
				errorPlacement: function(error, element) {
					error.appendTo( element.next().next().next());
				} 
		})
});
function validateFile(){
	var $n = jQuery.noConflict();   
	if($n('#currImg').length>0){
		return true;
	}
	var fragment = $n("#image_name").val();
	var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");  
	var imageid=$n("#image_name").val();

	if(imageid==""){
		if(filename!="")
			return true;
		else
			{
			$n("#err_daynamic").remove();
			$n("#image_name").after('<label class="image_error" id="err_daynamic">Please select file.</label>');
			return false;  
		} 
	}
	else{
		return true;
	}      
}
function reloadfileupload(){

	var $n = jQuery.noConflict();  
	var fragment = $n("#image_name").val();
	var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");
	var validExtensions=new Array();
	validExtensions[0]='jpg';
	validExtensions[1]='jpeg';
	validExtensions[2]='png';
	validExtensions[3]='gif';
	validExtensions[4]='bmp';
	validExtensions[5]='tif';
	var extension = filename.substr( (filename.lastIndexOf('.') +1) ).toLowerCase();
	var inarr=parseInt($n.inArray( extension, validExtensions));
	if(inarr<0){
		$n("#err_daynamic").remove();
		$n('#fileuploaddiv').html($n('#fileuploaddiv').html());   
		$n("#image_name").after('<label class="image_error" id="err_daynamic">Invalid file extension</label>');
	}
	else{
		$n("#err_daynamic").remove();
	} 
}


function  confirmDelete(){
	var agree=confirm("Are you sure you want to delete cover carousel image ?");
	if (agree)
		return true ;
	else
		return false;
}