# MODX Revolution: latest posts from Instagram
This snippet allows us to get the latest posts from any Instagram user.

N.B.! Please take into account, 20 latest posts can be returned only as it's limited by Instagram.

|    Property   |                                           Description                                          |     Default    |
|:-------------:|:----------------------------------------------------------------------------------------------:|:--------------:|
| &accountName  | Instagram account name                                                                         |                |
| &limit        | Set the limit on the maximum number of items that will be displayed                            |        6       |
| &showVideo    | Do we need to show the video as well? Available options: 1, 0                          |      0     |
| &imageQuality | Set the image quality. Available options: low_resolution, thumbnail, standard_resolution     | low_resolution |
| &videoQuality | Set the video quality. Available options: low_resolution, standard_resolution, low_bandwidth | low_resolution |

Installation
---------
1. Create the snippet called InstagramLatestPosts and copy the snippet code there
2. Create two chunks: 
* Instagram-Inner
* Instagram-Outer
3. Copy the corresponding HTML code to the chunks above
4. Place the snippet call in MODX where it's needed
```
[[!InstagramLatestPosts? &accountName=`nike`]]
```
5. Modify the properties if you like as shown below

Usage
---------
```
[[!InstagramLatestPosts?
	&accountName=`nike`
	&limit=`10`
	&showVideo=`1`
	&imageQuality=`low_resolution`
	&videoQuality=`low_bandwidth`
]]