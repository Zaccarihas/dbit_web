<html>
    <head>
        <title>Account form</title>
        <link rel="stylesheet" href="view/style.css">
    <head>

    <body>
        <form id="frm_account" method="post" action="controller/account_new.php">
            <div id="account_id">NEW
                <input type="hidden" name="account_id" value="">
            </div>
            <fieldset id="fldset_desc"> 

                <div class="formfield">
                    <label for="label">Label</label>
                    <input type="text" id="label" name="label" value="" placeholder="Kontots etikett eller kod" pattern="[A-Za-z0-9]+" required/>
                </div>
                
                <div class="formfield">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="" placeholder="Kontots namn" required/>
                </div>

                <div class="formfield">
                    <label for="parent">Parent</label>
                    <input type="text" id="parent" name="parent" value="" placeholder="Överställt kontos id-nummer"/>
                </div>

                <div class="formfield">
                    <label for="trustee">Trustee</label>
                    <input type="text" id="trustee" name="trustee" value="" placeholder="Förvaltare av kontot"/>
                </div>
                
                <div class="formfield">
                    <label for="alias_id">Alias</label>
                    <input type="text" id="alias_id" name="alias_id" value="" placeholder="Kontots alias hos förvaltaren">
                </div>
                
                <div class="formfield">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" rows="5"></textarea>
                </div>

            </fieldset>
            <fieldset id="fldset_meta">
                
                <div class="formfield">
                    <label for="upper_limit">Upper limit</label>
                    <input type="text" id="upper_limit" name="upper_limit" value="" placeholder="Övre beloppsgräns"/>
                </div>
                
                <div class="formfield">
                    <label for="lower_limit">Lower limit</label>
                    <input type="text" id="lower_limit" name="lower_limit" value="0" placeholder="Nedre beloppsgräns (negativt för kreditutrymme)"/>
                </div>

                <div class="formfield">
                    <label for="rate">Rate</label>
                    <input type="text" id="rate" name="rate" value="1" placeholder="Omvandlingsfaktor"/>
                </div>
            
            </fieldset>
            <fieldset id="fldset_control">
                <button class="formbuttons" type="submit" id="register" name="register">Register</button>
                <button class="formbuttons" type="button" onclick="window.location.href='lst_accounts.php'">Cancel</button>
            </fieldset>

        </form>

    </body>

</html>