const Install = (() => {

	function init(){
		if($('.performInstall').length){
			performStep(0);
		}
        if ($('.environment').length) {
            console.log('df');
            bindDatabaseField();
        }
	};

	function performStep(stepNumber)
	{
		var step = $($('.performInstall .step')[stepNumber]);
		if(step.length){
			step.addClass('font-weight-bold');
			$.ajax({
				url: step.data('url'),
				dataType: 'json'
			}).fail(function(data){
				populateErrors(data, step);
			}).done(function(){
				step.find('i').removeClass('d-none');
				step.removeClass('font-weight-bold');
				performStep(stepNumber+1)
			});
		}
		else{
			$('.performInstall p.success, .performInstall a.visit').removeClass('d-none');
		}
	}

	function populateErrors(data, step)
	{
		$('.error').removeClass('d-none').find('.message').html(data.responseJSON.message);
		step.find('i').removeClass('fa-check d-none').addClass('fa-times');
	}

    function bindDatabaseField()
    {
        $('#DB_DATABASE').focusout(function(){
            let name = $(this).val().trim();
            let input = $(this);
            if (name == '') {
                return;
            }
            $.ajax({
                url: '/checkDatabase',
                dataType: 'json',
                data: {database: name}
            }).done(function(data){
                if (data.exists) {
                    input.prev().removeClass('d-none');
                } else {
                    input.prev().addClass('d-none');
                }
            })
        });
    }

	return {
		init:init
	};

})();

$(() => {
	Install.init();
});