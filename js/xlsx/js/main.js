//Initialize function
var init = function () {
    // TODO:: Do your initialization job
    console.log("init() called");

    // add eventListener for tizenhwkey
    document.addEventListener('tizenhwkey', function(e) {
        if(e.keyName == "back")
            tizen.application.getCurrentApplication().exit();
    });    
    
    $('#save').on('click', function() {
    	
        var file = {
            worksheets: [[]], // worksheets has one empty worksheet (array)
            creator: 'John Smith', created: new Date('8/16/2012'),
            lastModifiedBy: 'Larry Jones', modified: new Date(),
            activeWorksheet: 0
        }, w = file.worksheets[0]; // cache current worksheet
        w.name = $('#WName').val();
        $('#Worksheet1').find('tr').each(function() {
            var r = w.push([]) - 1; // index of current row
            $(this).find('input').each(function() { w[r].push(this.value); });
        });
        console.log("Excel data "+ xlsx(file).href());
        window.location.href = xlsx(file).href() ;
       
    });
};
$(document).bind('pageinit', init);




function test()
{	
	
	zip.file("hello.txt", "Hello, World!").file("tempfile", "nothing");
	zip.folder("images").file("smile.gif", base64Data, {base64: true});
	zip.file("Xmas.txt", "Ho ho ho !", {date : new Date("December 25, 2007 00:00:01")});
	zip.remove("tempfile");
	base64zip = zip.generate();	
}