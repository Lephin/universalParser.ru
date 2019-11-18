var http = new XMLHttpRequest();
var arrayValidate = [];
var i = 0;

function createValidateRule()
{
    
    let validateAddColumns = document.getElementById('validateAddColumns');
    let validateRules = document.getElementById('validateRules');
    let validateRulesView = document.getElementById('validateRulesView');

    if (validateAddColumns.value !== '') {
           
        arrayValidate[i] = [
            validateAddColumns.value,
            '',
            validateRules.value
        ];
        
        let htmlInner = '<li id="validateLicolumns" style="list-style-type: none;">' + validateAddColumns.value + '</li>';      
        validateRulesView.insertAdjacentHTML('afterBegin',htmlInner);
    
        test = JSON.stringify(arrayValidate);

        http.open('GET','/site/index?value=' + test,true);   

        http.onload = function () {
            var test2 = JSON.parse(http.responseText);
            
            validateLicolumns = document.getElementById('validateLicolumns');
           
            if (validateLicolumns) {
            //    console.log(validateLicolumns);
            //    validateRulesView.removeChild(validateLicolumns);
            }
            for (var i2 = 0;i2 < test2.length;i2++) {
            //    for (var i3 = 0;i3 < test2[i2].length;i3++) {
                  
                 //   console.log(test2[i2][i3]);
             //   }
                
            }
        };

        http.send();

        i++;
    }
}


