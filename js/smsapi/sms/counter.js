// Create the class

var mySmsapiCounter = Class.create({   
       
    initialize:function(eventToObserve) // Is called when the page has finished loading by the Event.observe code below
	{
	
		var counter = document.getElementById('counter');
        var counterContainer = document.getElementById('counterContainer');
        var maxchars = 160;
        var childAdded = false;
        var activeTextArea = false;
        var textAreaId = false;

		$('sms_templates').observe(eventToObserve, function(event) {
			var textlength = 0;
			activeTextArea = event.findElement('textarea');

			if(activeTextArea) {
                
                counterContainer.show(); //snow counter div at starts

                if (textAreaId != activeTextArea.id) {
                    counterContainer.remove(); //remove old counterContainer
                    activeTextArea.insert({ //reinitialize conterContainer in new position
                        after: counterContainer
                    });   
                }

				textlength = activeTextArea.value.length;
				counter.update(textlength);

                if(textlength<=(maxchars-50)) {

                    $('counter').setStyle({
                            border:'0px',
                            fontWeight:' normal',
                            color: 'inherit',
                            backgroundColor: 'inherit'
                     });
                     $('toolongalert').hide();

                } 
                else if(textlength<=(maxchars-20)) {

                    $('counter').setStyle({
                            border:'1px solid orange',
                            fontWeight:' normal',
                            color: 'black',
                            backgroundColor: 'yellow'
                     });
                     $('toolongalert').hide();

                } else {
                    
                    $('counter').setStyle ({
                            border:'1px solid red',
                            fontWeight:' bold',
                            color: 'white',
                            backgroundColor: '#900'
                     });
                     $('toolongalert').show();

                }

            
			}            
            

            
            
		});	
		
	}   
    
});
// Global variable for the instance of the class
// Creating an instance of the class if the page has finished loading
Event.observe(window, 'load', function() {
    document.getElementById('counterContainer').hide(); //hide counter div at start
	new mySmsapiCounter('click');
    new mySmsapiCounter('keyup');
});