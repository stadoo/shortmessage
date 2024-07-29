$(document).ready(function () {
  $("[id^=share-button-]").on("click", function () {
    var postId = $(this).attr("id").split("-")[2];
    var modal = new bootstrap.Modal(
      document.getElementById("share-modal-" + postId)
    );
    modal.show();
  });

  $(".share-link").on("click", function (e) {
    e.preventDefault();
    var network = $(this).data("network");
    var postId = $(this).closest(".modal").attr("id").split("-")[2];
    var url = "";
    var postUrl = window.location.href + "/view/" + postId;
    var postTitle = $("h1").text();

    switch (network) {
      case "facebook":
        url =
          "https://www.facebook.com/sharer/sharer.php?u=" +
          encodeURIComponent(postUrl);
        break;
      case "whatsapp":
        url =
          "https://api.whatsapp.com/send?text=" +
          encodeURIComponent(postTitle + " " + postUrl);
        break;
      case "twitter":
        url =
          "https://twitter.com/intent/tweet?url=" +
          encodeURIComponent(postUrl) +
          "&text=" +
          encodeURIComponent(postTitle);
        break;
      case "linkedin":
        url =
          "https://www.linkedin.com/sharing/share-offsite/?url=" +
          encodeURIComponent(postUrl);
        break;
    }

    window.open(url, "Share Dialog", "width=626,height=436");
    var modal = bootstrap.Modal.getInstance(
      document.getElementById("share-modal-" + postId)
    );
    modal.hide();
  });
});
