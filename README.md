[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/dsRPaEFS)
# CSE330

http://ec2-3-20-203-119.us-east-2.compute.amazonaws.com/~zihozoseph03/login.html

Creative Portion:
- The design of Welcome "name" is added as you upload
- Deletion of the image is only determined by the owner of the uploader

JOSEPHJANG-472920-JOSEPHJ05

# Regrade
| Earned | Possible | Requirement                                                                                                   | Feedback |
| ------ | -------- | ------------------------------------------------------------------------------------------------------------- | -------- |
| 2      | 4        | Users should not be able to see any files until they log in, users.txt should not be accessible to the public |-2 points. `user.txt` is within `public_html/` and so is publicly available.          |
| 4      | 4        | User can see a list of uploaded files                                                                         |          |
| 4      | 5        | Users can open uploaded files                                                                                 |-1 files are downloaded instead of opened in the browser          |
| 4      | 4        | Users can delete files                                                                                        |          |
| 2      | 4        | Users can upload files, files are stored securely                                                             |-2 points. All uploaded files are served by apache (they're in `public_html/`). This is incorrect          |
| 0      | 2        | Directory structure is not exposed                                                                            | -2 points. the file structure that you use to store uploaded files is exposed in the `href` of the a tags that display the files for downloading.          |
| 2      | 2        | User can log out                                                                                              |          |
| 4      | 4        | Code is well formatted and easy to read                                                                       |          |
| 1.5      | 3        | Site follows FIEO                                                                                             |-1.5 Inconsistent filtering of output. see: line 56 and 62 in `functions.php`, for example           |
| 2.5      | 3        | All pages pass the W3C validator                                                                              |-.5 points because when a filename contains a space, the validator fails on the page that lists the files          |
| 3      | 4        | Site is intuitive to use and navigate                                                                         |-1 can't view files unless I successfully upload a file first           |
| 0      | 1        | Site is aesthetically pleasing                                                                                | no css          |

## Creative Portion (15 possible)

| Earned | Feature | Feedback |
| ------ | ------- | -------- |

## Grade

| Total Earned | Total Possible |
| ------------ | -------------- |
| 0            | 55             |
