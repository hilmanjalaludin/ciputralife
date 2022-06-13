Ext.document('document').ready(function() {
    Ext.DOM.Transaction();
});

Ext.DOM.Transaction = function() {
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_transaction',
            CustomerId: Ext.Cmp("CustomerId").getValue()
        }
    }).load("Transaction")
}

Ext.DOM.InsuredWindow = function(option) {
    if (option.checked) {
        var dialog = Ext.Window({
            url: 'form.edit.axa.product.php',
            width: parseInt(Ext.DOM.screen.availWidth - 300),
            height: parseInt(Ext.DOM.screen.availHeight - 200),
            name: 'WinEditInsured',
            param: {
                action: 'ShowData',
                CampaignId: Ext.Cmp('CampaignId').Encrypt(),
                InsuredId: Ext.BASE64.encode(option.value)
            }
        });
        dialog.popup();
    }
}