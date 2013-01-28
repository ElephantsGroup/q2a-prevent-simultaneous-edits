====================================
Question2Answer Prevent Simultaneous Edits v0.1
====================================
-----------
Description
-----------
This is a plugin for **Question2Answer** that prevents simultaneous post edits by your users. Issue: If two users are editing the same question at the same time, they do not know from each other. Editor A submits his version, afterwards editor B submits his version, then the server overwrites first version A with version B, version A is lost. 

--------
Features
--------
- this plugin prevents simultaneous edits of posts by informing the users if a post is currently edited 
- user B will get a javascript alert, and is redirected to the post instead of the edit page

------------
Installation
------------
#. Install Question2Answer_
#. Get the source code for this plugin directly from github_
#. Extract the files.
#. Change language strings in file **qa-prevent-simultaneous-edits-lang.php**
#. Upload the files to a subfolder called ``prevent-simultaneous-edits`` inside the ``qa-plugins`` folder of your Q2A installation.
#. Navigate to your site, go to **Admin -> Plugins** on your q2a install. You will see the notice: 'The Create Table for Simultaneous Edits module requires some *database initialization*.'
#. Click on *database initialization*. Then click button **Install**.
#. The MySql table 'qa_edit_preventer' will be created. Notice appears: The module has completed database initialization.
#. You are done! 
#. Test the functionality by editing one post, open another browser, login with another user account, try to edit the same post. You should get a notice now!

.. _Question2Answer: http://www.question2answer.org/install.php
.. _github: https://github.com/echteinfachtv/q2a-prevent-simultaneous-edits

----------
Disclaimer
----------
This is **beta** code. It is probably okay for production environments, but may not work exactly as expected. You bear the risk. Refunds will not be given!

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

-------
Copyright
-------
All code herein is OpenSource_. Feel free to build upon it and share with the world.

.. _OpenSource: http://www.gnu.org/licenses/gpl.html

---------
About q2a
---------
Question2Answer is a free and open source platform for Q&A sites. For more information, visit: www.question2answer.org

---------
Final Note
---------
If you use the plugin:
+ Consider joining the Question2Answer-Forum_, answer some questions or write your own plugin!
+ You can use the code of this plugin to learn more about q2a-plugins. It is commented code.
+ Thanks!

.. _Question2Answer-Forum: http://www.question2answer.org/qa/

