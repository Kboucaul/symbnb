
$('#add-image').click(() => {
    //JE recupere le numero des futurs champs
    const index = +$('#widgets-counter').val();

    //Je recupere le proptotype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);
    
    // J'injecte ce code au sein de ma div
    $('#ad_images').append(tmpl);

    //On increment le widgets-counter qui nous donne les id des sous-form
    //=>eviter le pb de deux div ayant meme id.

    $('#widgets-counter').val(index + 1); 

    // Je gere le boutton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons()
{
    $('button[data-action="delete"]').click(function()
    {
        const target = this.dataset.target;
        //trouve moi la target (la div) et supprime la
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}
handleDeleteButtons();
updateCounter();