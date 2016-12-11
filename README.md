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
| &innerTpl | Inner chunk name | Instagram-Inner |
| &outerTpl | Outer chunk name | Instagram-Outer |
| &errorTpl | Error chunk name | Instagram-Error |

Installation
---------
1. Create the snippet called InstagramLatestPosts and copy the snippet code there
2. Create three chunks with the following names
  * Instagram-Outer
  * Instagram-Inner
  * Instagram-Error
3. [optional] You can modify the chunk names above; if you do that please specify these names in the snippet parameters
4. Copy the corresponding HTML code to the chunks above
5. [optional] You can modify the chunk code as well; if you do that please use the following placeholders:
  * Instagram-Outer
  
  | Placeholder     | Description                       |
  |-----------------|-----------------------------------|
  | [[+accountUrl]] | The link to the Instagram profile |
  | [[+items]]      | The items returned from Instagram |
  
  * Instagram-Inner
  
  | Placeholder | Description                                                     |
  |-------------|-----------------------------------------------------------------|
  | [[+link]]   | The direct link to the corresponding post                       |
  | [[+type]]   | The type of the item; it can have two values only: image, video |
  | [[+url]]    | URL of the image or video depending on what you want to show    |
  
  * Instagram-Error

  | Placeholder | Description                    |
  |-------------|--------------------------------|
  | [[+error]]  | The error explaining the issue |

6. Place the snippet call in MODX where it's needed
```
[[!InstagramLatestPosts? &accountName=`nike`]]
```
7. Modify the properties if you like as shown below

Usage
---------
```
[[!InstagramLatestPosts?
	&accountName=`nike`
	&limit=`10`
	&showVideo=`1`
	&imageQuality=`low_resolution`
	&videoQuality=`low_bandwidth`
	&innerTpl=`MyInnerTemplate`
	&outerTpl=`MyOuterTemplate`
	&errorTpl=`MyErrorTemplate`
]]