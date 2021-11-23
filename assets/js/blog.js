// Global var for image data
var img_base64 = null;
var comment_id_edit = null;

// Handle modals
$('#new-post-button').click(function() {
    $('#new-post-modal').modal('toggle');
})

$('#edit-post-button').click(function() {
    $('#edit-post-modal').modal('toggle');
})

$('#search-button').click(function() {
    $('#search-modal').modal('toggle');
})

$('#profile-search-button').click(function() {
    $('#search-modal').modal('toggle');
})

$('.comment-edit-button').click(function() {
    $('#edit-comment-modal').modal('toggle');
})


// Handle form submissions

function submitLogin ()
{
    var pass = $("#login-password-field")[0].value;
    var user = $("#login-username-field")[0].value;
    var myData = JSON.stringify({"username": user, "password": pass});

    $.ajax({
        url: '/blog/api/user/',
        type: 'post',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("Invalid login. Have you registered yet?");
                $("#login-form")[0].reset();
            }
            else
            {
                window.location.replace("profile.php");
            }
        }
    });
}

$('#login-form').submit(function (e) {
    e.preventDefault();
});

function submitRegister ()
{
    var pass = $("#register-password-field")[0].value;
    var user = $("#register-username-field")[0].value;
    var myData = JSON.stringify({"username": user, "password": pass});

    $.ajax({
        url: '/blog/api/user/',
        type: 'put',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Maybe an account is already registered with that name");
                $("#register-form")[0].reset();
            }
            else
            {
                window.location.replace("/blog/");
            }
        }
    });
}

$('#register-form').submit(function (e) {
    e.preventDefault();
});


function newPost ()
{
    var img = null;
    var post_title = $("#new-post-title")[0].value;
    var post_body = $("#new-post-body")[0].value;
    var filename = $("#new-post-file-input")[0].value.replace(/.*[\/\\]/, '');

    // Wait for file to load
    while (img === null)
    {
        img = img_base64;
    }

    // Build out data for request
    var filetype = filename.substr(filename.indexOf("."));
    var extra = {"title": post_title, "image": img, "image_type": filetype};
    var myData = JSON.stringify({"post_text": post_body, "extra": extra});

    // Make request
    $.ajax({
        url: '/blog/api/post/',
        type: 'post',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Make sure all inputs are valid");
                $("#register-form")[0].reset();
            }
            else
            {
                // alert(result);
                window.location.replace("profile.php");
            }
        }
    });
}


$('#new-post-form').submit(function (e) {
    e.preventDefault();
});


// Got this code snippit from stackoverflow
function readFile(file) {
    var reader = new FileReader();
    reader.onload = readSuccess;
    function readSuccess(evt)
    {
        var r = evt.target.result;
        img_base64 = r.substr(r.indexOf(",") + 1);
    };
    reader.readAsDataURL(file);
}

$("#new-post-file-input").change(function () {
    readFile($("#new-post-file-input")[0].files[0]);
});


function deletePost(postID)
{
    var myData = JSON.stringify({"id": postID});

    // Make request
    $.ajax({
        url: '/blog/api/post/',
        type: 'delete',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Make sure all inputs are valid");
            }
            else
            {
                alert(result);
                window.location.replace("/blog/");
            }
        }
    });
}


function updatePost(postID)
{
    var post_title = $("#edit-post-title")[0].value;
    var post_body = $("#edit-post-body")[0].value;

    var myData = JSON.stringify({"post_id": postID, "post_title": post_title, "post_text": post_body});

    // Make request
    $.ajax({
        url: '/blog/api/post/',
        type: 'put',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Make sure all inputs are valid");
            }
            else
            {
                window.location.reload(false);
            }
        }
    });
}


function deleteComment(commentID)
{
    var myData = JSON.stringify({"id": commentID});

    // Make request
    $.ajax({
        url: '/blog/api/comment/',
        type: 'delete',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Make sure all inputs are valid");
            }
            else
            {
                window.location.reload(false);
            }
        }
    });
}

function newComment(postID)
{
    var comment_body = $("#comment-textarea")[0].value;
    var myData = JSON.stringify({"post_id": postID, "comment_text": comment_body});

    // Make request
    $.ajax({
        url: '/blog/api/comment/',
        type: 'post',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Make sure all inputs are valid");
            }
            else
            {
                window.location.reload(false);
            }
        }
    });
}

$('#new-comment-form').submit(function (e) {
    e.preventDefault();
});

/**
 * This is called when the edit button is clicked on a comment to select the comment to edit
 */
function updateCommentEditID(commentID)
{
    comment_id_edit = commentID;
}

function updateComment()
{
    var comment_id = null;

    // Awaits the comment to be selected
    while (comment_id == null)
    {
        comment_id = comment_id_edit;
    }

    var comment_body = $("#edit-comment-body")[0].value;
    var myData = JSON.stringify({"id": comment_id, "comment_text": comment_body});

    // Make request
    $.ajax({
        url: '/blog/api/comment/',
        type: 'put',
        data: myData,
        success: function(result)
        {
            if (!result)
            {
                alert("That didn't work. Make sure all inputs are valid");
            }
            else
            {
                window.location.reload(false);
            }
        }
    });
}

