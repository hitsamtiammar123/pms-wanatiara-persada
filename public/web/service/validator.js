app.service('validator',function(alertModal,unitFilter){
    this.validateNum=function(value){
        var regexNum=/\d*%$/;
        var regexNonNum=/\D+%$/;
        if(!regexNum.test(value)||regexNonNum.test(value)){
            return false;
        }

        return true;
    }

    this.validateGrade=function(value){
        var regexGrade=/[A-F][+|-]?$/;
        if(!regexGrade.test(value)&&value!==''){
            return false;
        }

        return true;
    }

    this.isNum=function(t){
        var numRegex=/^(\d{1,3}\,?)+/;
        var percentRegex=/\d+%$/;
        var stripRegex=/^-$/;

        t=t.trim();
        return numRegex.test(t)||percentRegex.test(t)||
        stripRegex.test(t);
    }

    this.isChar=function(t){
        var latinRgx=/[\u0021-\u007E]+/;
        var nonAsciiRgx=/[^\u0000-\u007F]+/;
        

        t=t.trim();
        return latinRgx.test(t)||nonAsciiRgx.test(t);
    }

    this.isUnitFilter=function(t){
        t=t.trim().toLowerCase();
        return unitFilter.indexOf(t)!==-1||unitFilter.toString().indexOf(t)!==-1
    }
}) 