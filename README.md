# cryptnotes
A quick bootstrap 5 blowfish jQuery PHP encrypted diary script

* author: Stephen Phillips
* date: 14/10/2024
* version: 1

# intro
A very simple example of using client side JS encryption with Ajax and PHP to create and store private diary notes, with PHP used to then save and load the content.
Makes use of the standalone Blowfish library from Dojo Toolkit: blowfish.js over at https://www.sladex.org/blowfish.js/ for encryption and decryption of text.

# description/features

* The script is mobile responsive and features a toggle button to hide the list of diary entries.
* It has a view tab and an editor tab, along with a save, reset and decrypt button that allows you to decrypt contents to edit it.
* Content can be edited in either window and will be saved to the filename specified in the filename input field using the specified decrypt key in the input box below.
* ECB ecryption is used as standard, but this can be changed by altering the parameters of the encrypt/decrypt function.
* The secret key used for encryption is based off of the password the user is requested to enter when loading the page, I would suggest serving via HTTPS SSL, and using a private tab in your browser to further ensure security also.
* The key used to encrypt files is then made up of 2 parts, this user password, and the filename without the .txt extension, this can be set by hand in the filname input field for new entries, which will update the decrypt key as you type it for new entries, and defaults to the current date in a YYYYMMDD format.
* When you press save the contents of the active editor window will be encrypted and saved to the specified file regardless of if it is decrypted (might ad code to avoid this in future).
* Previous entries can be loaded from the filelist simply by clicking on their filename and load into the initial "Log Content" editor, the filename and decrypt key will then also be updated and if you entered the correct initial password part of the key you will be able to decrypt to view the text by pressing the decrypt button.
* The idea of the second tab with the Edit Content editor was so that you can edit, copy and paste an entry to later save, whilst going through previous entries, as it persists until the page is refreshed, but do remeber to ensure that the filename and key are correct when saving so that you do not overwrite an older note as those values will update for the notes you load into the "Log Content" editor as you do so. (I might add checking and a confirmation when saving to help avoid this in future as an update too).
* The system WILL overwrite existing notes if they have the same filename as you specify with out any warning or confirmation when you hit save, this is true for if you type an existing filename into the filename input box, or if you load one from the filelist also.

# requirements
A webserver with PHP installed and a browser that supports modern HTML 5 and JS with write permissions on the directory under the root of this script called logs, which is where new diary entries get stored.

# notes
I did think after writing this that you could almost use it to make a simple message board/IM system, by adding user supports and perhaps private and public key encryption instead for messages between users, that would operate in a threaded basis with no need for a DB.