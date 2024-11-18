function copyTextFromBlock(id, initiator) {

    var initiatorOriginalText = initiator.innerHTML;

    /* Get the text field */
    var copyText = document.getElementById(id);
    if (!copyText) {
        return;
    }
    var tmp = document.createElement("textarea");
    document.body.append(tmp);

    tmp.innerHTML = copyText.innerHTML;

    /* Select the text field */
    tmp.select();

    /* Copy the text inside the text field */
    document.execCommand("copy");

    tmp.parentNode.removeChild(tmp);

    initiator.innerHTML = '<span style="color: #777">OK</span>';
    setTimeout(function (){
        initiator.innerHTML = initiatorOriginalText;
    }, 3000)
}