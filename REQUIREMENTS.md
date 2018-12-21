## Amazon S3 bucket preparation

You need to set up CORS in the S3 bucket per [Amazon's documentation](https://aws.amazon.com/developers/getting-started/browser/).


## Uploading to S3

### Solution 1

AWS STS (https://stackoverflow.com/questions/36208123/how-can-i-use-a-backend-pre-signed-url-to-upload-files-to-amazon-s3-using-its-sd)
p.allow(
    actions: ['s3:PutObject'],
    resources: ['arn:aws:s3:::some-bucket/path/to/xxx*'],
    effect: 'allow'
  )
  
we get ==> access and secret

Send to browser

Use AWS S3 JS API to upload



We only need to get STS creds ONCE since each user uploads to their own folder
Grant creds for 24 hours
 
 
### Solution 2

See https://stackoverflow.com/questions/22531114/how-to-upload-to-aws-s3-directly-from-browser-using-a-pre-signed-url-instead-of

For each file XHR to server and get a presigned URL

index.php
    view        Upload
    task        presigned
    filename    akeeba.Upload.files[currentFileID].filename
    mime        akeeba.Upload.files[currentFileID].type

var xhr = new XMLHttpRequest();
xhr.open('PUT', signedUrl, true);
xhr.setRequestHeader('Content-Type', signedUrlContentType);
xhr.onload = () => {
  if (xhr.status === 200) {
    // success!
  }
};
xhr.onerror = () => {
  // error...
};
xhr.send(file); // `file` is a File object here 

xhr.upload.onprogress = (event) => {
  if (event.lengthComputable) {
    var percent = Math.round((event.loaded / event.total) * 100)
    console.log(percent);
  }
};