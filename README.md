# FOS Uploader

A web application to let event guests upload pictures.

## Summary

The concept is that during an event many people have smartphones or even consumer-grade DSLRs. They snap photos and videos
from angles that the photographer or videographer hired to cover the event cannot possibly cover. Some of these may be of 
good enough quality to include in the video of the event or even in the event's photo album.

Each event is assigned a shortcode you need to know before you can upload photos into its storage area. The shortcode can
be given to the event's guests as-is or as a QR code they can scan with their phones (tha backend of this app generates the
QR code for you). Photos are uploaded to an Amazon S3 bucket under a directory specified per event and inside a subdirectory
the name of which is derived by the name of the client.

## License

This project is given away under the GNU Affero General Public License version 3 of the license or, at your choice, any later
version published by the Free Software Foundation. You can find the full license text [here](https://www.gnu.org/licenses/agpl-3.0.html).

## Credits

The PHP code was written by [Nicholas Dionysopoulos](https://www.dionysopoulos.me).

The UX / UI design and frontend development was done by [Lucid Fox](https://www.lucid-fox.com).

This application uses the [Akeeba Web Framework](https://github.com/akeeba/awf).

## About the name

This project was originally made for Fos Photography, based in Piraeus, Greece. However, they never paid us for our work. As
stipulated in the contract they signed we withhold the copyright until full payment. It's been three months since project
delivery and two months of sending them emails reminding them to pay us which they will not even answer. Since we still have
the copyright of the application and we understand we're never gonna be paid for our work we decided to open-source this
application.
