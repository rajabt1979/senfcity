import 'dart:io';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/ui/common/ps_hero.dart';
import 'package:fluttercity/ui/common/ps_square_progress_widget.dart';
import 'package:fluttercity/viewobject/default_icon.dart';
import 'package:fluttercity/viewobject/default_photo.dart';
import 'package:fluttertoast/fluttertoast.dart';

class PsNetworkImage extends StatefulWidget {
  const PsNetworkImage(
      {Key? key,
        required this.photoKey,
        required this.defaultPhoto,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.fill})
      : super(key: key);

  final double? width;
  final double? height;
  final Function? onTap;
  final String? photoKey;
  final BoxFit boxfit;
  final DefaultPhoto? defaultPhoto;


  @override
  State<PsNetworkImage> createState() => _PsNetworkImageState();
}

class _PsNetworkImageState extends State<PsNetworkImage> {
  double? width;
  double? height;
  @override
  Widget build(BuildContext context) {

    if (widget.width == double.infinity) {
      width = MediaQuery.of(context).size.width;
    } else {
      width = widget.width;
    }
    if (widget.height == double.infinity) {
      height = MediaQuery.of(context).size.height;
    } else {
      height = widget.height;
    }
    if (widget.defaultPhoto!.imgPath == '') {
      return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: Image.asset(
            'assets/images/placeholder_image.png',
            width: widget.width,
            height: widget.height,
            fit: widget.boxfit,
          ));
    } else {
      final String fullImagePath =
          '${PsConfig.ps_app_image_url}${widget.defaultPhoto!.imgPath}';
      final String thumbnailImagePath =
          '${PsConfig.ps_app_image_thumbs_url}${widget.defaultPhoto!.imgPath}';
      // print(
      //     'tag : $photoKey${PsConfig.ps_app_image_url}${defaultPhoto.imgPath}');
      return PsHero(
        transitionOnUserGestures: true,
        tag: widget.photoKey!,
        child: GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: CachedNetworkImage(
            placeholder: (BuildContext context, String url) {
              if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                return CachedNetworkImage(
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                  placeholder: (BuildContext context, String url) {
                    return PsSquareProgressWidget();
                  },
                  imageUrl: thumbnailImagePath,
                );
              } else {
                return PsSquareProgressWidget();
              }
            },
            width: width,
            height: height,
            fit: widget.boxfit,
            imageUrl: fullImagePath,
            errorWidget: (BuildContext context, String url, Object? error) =>
                Image.asset(
                  'assets/images/placeholder_image.png',
                  // width: widget.width,
                  // height: widget.height,
                  fit: widget.boxfit,
                ),
          ),
        ),
      );
    }
  }
}

class PsNetworkImageWithUrl extends StatefulWidget {
  const PsNetworkImageWithUrl(
      {Key? key,
        required this.photoKey,
        required this.imagePath,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.cover})
      : super(key: key);

  final double? width;
  final double? height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final String imagePath;


  @override
  State<PsNetworkImageWithUrl> createState() => _PsNetworkImageWithUrlState();
}

class _PsNetworkImageWithUrlState extends State<PsNetworkImageWithUrl> {
  double? width;
  double? height;
  @override
  Widget build(BuildContext context) {
    if (widget.width == double.infinity) {
      width = MediaQuery.of(context).size.width;
    } else {
      width = widget.width;
    }
    if (widget.height == double.infinity) {
      height = MediaQuery.of(context).size.height;
    } else {
      height = widget.height;
    }

    if (widget.imagePath == '') {
      return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: Image.asset(
            'assets/images/placeholder_image.png',
            width: widget.width,
            height: widget.height,
            fit: widget.boxfit,
          ));
    } else {
      final String fullImagePath = '${PsConfig.ps_app_image_url}${widget.imagePath}';
      final String thumbnailImagePath =
          '${PsConfig.ps_app_image_thumbs_url}${widget.imagePath}';

      if (widget.photoKey == '') {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: CachedNetworkImage(
            placeholder: (BuildContext context, String url) {
              if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                return CachedNetworkImage(
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                  placeholder: (BuildContext context, String url) {
                    return PsSquareProgressWidget();
                  },
                  imageUrl: thumbnailImagePath,
                );
              } else {
                return PsSquareProgressWidget();
              }
            },
            width: width,
            height: height,
            fit: widget.boxfit,
            imageUrl: fullImagePath,
            errorWidget: (BuildContext context, String url, Object? error) =>
                Image.asset(
                  'assets/images/placeholder_image.png',
                  width:width,
                  height: height,
                  fit: widget.boxfit,
                ),
          ),
        );
      } else {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: CachedNetworkImage(
            placeholder: (BuildContext context, String url) {
              if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                return CachedNetworkImage(
                  width:width,
                  height: height,
                  fit: widget.boxfit,
                  placeholder: (BuildContext context, String url) {
                    return PsSquareProgressWidget();
                  },
                  imageUrl: thumbnailImagePath,
                );
              } else {
                return PsSquareProgressWidget();
              }
            },
            width: width,
            height: height,
            fit: widget.boxfit,
            imageUrl: fullImagePath,
            errorWidget: (BuildContext context, String url, Object? error) =>
                Image.asset(
                  'assets/images/placeholder_image.png',
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                ),
          ),
        );
      }
    }
  }
}

class PsFileImage extends StatelessWidget {
  const PsFileImage(
      {Key? key,
        required this.photoKey,
        required this.file,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.cover})
      : super(key: key);

  final double? width;
  final double? height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final File? file;

  @override
  Widget build(BuildContext context) {
    if (file == null) {
      return GestureDetector(
          onTap: onTap as void Function()?,
          child: Image.asset(
            'assets/images/placeholder_image.png',
            width: width,
            height: height,
            fit: boxfit,
          ));
    } else {
      if (photoKey == '') {
        return GestureDetector(
            onTap: onTap as void Function()?,
            child: Image(
              image: FileImage(file!),
            ));
      } else {
        return GestureDetector(
            onTap: onTap as void Function()?,
            child: Image(
              image: FileImage(file!),
            ));
      }
    }
  }
}

class PsNetworkCircleImage extends StatefulWidget {
  const PsNetworkCircleImage(
      {Key? key,
        required this.photoKey,
        this.imagePath,
        this.asset,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.cover})
      : super(key: key);

  final double? width;
  final double? height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final String ?imagePath;
  final String? asset;


  @override
  State<PsNetworkCircleImage> createState() => _PsNetworkCircleImageState();
}

class _PsNetworkCircleImageState extends State<PsNetworkCircleImage> {
  double? width;
  double? height;
  @override
  Widget build(BuildContext context) {
    if (widget.width == double.infinity) {
      width = MediaQuery.of(context).size.width;
    } else {
      width = widget.width;
    }
    if (widget.height == double.infinity) {
      height = MediaQuery.of(context).size.height;
    } else {
      height = widget.height;
    }
    if (widget.imagePath == null || widget.imagePath == '') {
      if (widget.asset == null || widget.asset == '') {
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Image.asset(
                  'assets/images/placeholder_image.png',
                  width: widget.width,
                  height: widget.height,
                  fit: widget.boxfit,
                )));
      } else {
        print('I Key : ${widget.photoKey}${widget.asset}');
        print('');
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: Hero(
              tag: '${widget.photoKey}${widget.asset}',
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Image.asset(widget.asset!,
                    width: widget.width, height: widget.height, fit: widget.boxfit),
              ),
            ));
      }
    } else {
      final String fullImagePath = '${PsConfig.ps_app_image_url}${widget.imagePath}';
      final String thumbnailImagePath =
          '${PsConfig.ps_app_image_thumbs_url}${widget.imagePath}';
      if (widget.photoKey == '') {
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(10000.0),
              child: CachedNetworkImage(
                placeholder: (BuildContext context, String url) {
                  if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                    return CachedNetworkImage(
                      width: width,
                      height: height,
                      fit: widget.boxfit,
                      placeholder: (BuildContext context, String url) {
                        return PsSquareProgressWidget();
                      },
                      imageUrl: thumbnailImagePath,
                    );
                  } else {
                    return PsSquareProgressWidget();
                  }
                },
                width: width,
                height:height,
                fit: widget.boxfit,
                imageUrl: fullImagePath,
                errorWidget: (BuildContext context, String url, Object? error) =>
                    Image.asset(
                      'assets/images/placeholder_image.png',
                      width: width,
                      height: height,
                      fit: widget.boxfit,
                    ),
              ),
            ));
      } else {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: Hero(
              tag: '${widget.photoKey}${widget.imagePath}',
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: CachedNetworkImage(
                  placeholder: (BuildContext context, String url) {
                    if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                      return CachedNetworkImage(
                        width: width,
                        height: height,
                        fit: widget.boxfit,
                        placeholder: (BuildContext context, String url) {
                          return PsSquareProgressWidget();
                        },
                        imageUrl: thumbnailImagePath,
                      );
                    } else {
                      return PsSquareProgressWidget();
                    }
                  },
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                  imageUrl: fullImagePath,
                  errorWidget:
                      (BuildContext context, String url, Object? error) =>
                      Image.asset(
                        'assets/images/placeholder_image.png',
                        width: width,
                        height: height,
                        fit: widget.boxfit,
                      ),
                ),
              )),
        );
      }
    }
  }
}

class PsFileCircleImage extends StatelessWidget {
  const PsFileCircleImage({
    Key? key,
    required this.photoKey,
    this.file,
    this.asset,
    this.width,
    this.height,
    this.onTap,
    this.boxfit = BoxFit.cover,
  }) : super(key: key);

  final double? width;

  final double? height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final File? file;
  final String? asset;

  @override
  Widget build(BuildContext context) {
    if (file == null) {
      if (asset == null || asset == '') {
        return GestureDetector(
            onTap: onTap as void Function()?,
            child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Container(
                    width: width,
                    height: height,
                    child: const Icon(Icons.image))));
      } else {
        print('I Key : $photoKey$asset');
        print('');
        return GestureDetector(
            onTap: onTap as void Function()?,
            child: Hero(
              tag: '$photoKey$asset',
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Image.asset(asset!,
                    width: width, height: height, fit: boxfit),
              ),
            ));
      }
    } else {
      if (photoKey == '') {
        return GestureDetector(
            onTap: onTap as void Function()?,
            child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Image(
                  image: FileImage(file!),
                )));
      } else {
        return GestureDetector(
          onTap: onTap as void Function()?,
          child: Hero(
              tag: file!,
              child: ClipRRect(
                  borderRadius: BorderRadius.circular(10000.0),
                  child: Image(image: FileImage(file!)))),
        );
      }
    }
  }
}

class PsNetworkImageWithUrlForUser extends StatefulWidget {
  const PsNetworkImageWithUrlForUser(
      {Key? key,
        required this.photoKey,
        required this.imagePath,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.cover})
      : super(key: key);

  final double? width;
  final double ?height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final String imagePath;


  @override
  State<PsNetworkImageWithUrlForUser> createState() => _PsNetworkImageWithUrlForUserState();
}

class _PsNetworkImageWithUrlForUserState extends State<PsNetworkImageWithUrlForUser> {
  double? width;
  double? height;
  @override
  Widget build(BuildContext context) {

    if (widget.width == double.infinity) {
      width = MediaQuery.of(context).size.width;
    } else {
      width = widget.width;
    }
    if (widget.height == double.infinity) {
      height = MediaQuery.of(context).size.height;
    } else {
      height = widget.height;
    }
    if (widget.imagePath == '') {
      return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: Image.asset(
            'assets/images/user_default_photo.png',
            width: widget.width,
            height: widget.height,
            fit: widget.boxfit,
          ));
    } else {
      final String fullImagePath = '${PsConfig.ps_app_image_url}${widget.imagePath}';
      final String thumbnailImagePath =
          '${PsConfig.ps_app_image_thumbs_url}${widget.imagePath}';

      if (widget.photoKey == '') {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: CachedNetworkImage(
            placeholder: (BuildContext context, String url) {
              if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                return CachedNetworkImage(
                  width: width,
                  height:height,
                  fit: widget.boxfit,
                  placeholder: (BuildContext context, String url) {
                    return PsSquareProgressWidget();
                  },
                  imageUrl: thumbnailImagePath,
                );
              } else {
                return PsSquareProgressWidget();
              }
            },
            width: width,
            height: height,
            fit: widget.boxfit,
            imageUrl: fullImagePath,
            errorWidget: (BuildContext context, String url, Object? error) =>
                Image.asset(
                  'assets/images/user_default_photo.png',
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                ),
          ),
        );
      } else {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: CachedNetworkImage(
            placeholder: (BuildContext context, String url) {
              if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                return CachedNetworkImage(
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                  placeholder: (BuildContext context, String url) {
                    return PsSquareProgressWidget();
                  },
                  imageUrl: thumbnailImagePath,
                );
              } else {
                return PsSquareProgressWidget();
              }
            },
            width: width,
            height: height,
            fit: widget.boxfit,
            imageUrl: fullImagePath,
            errorWidget: (BuildContext context, String url, Object? error) =>
                Image.asset(
                  'assets/images/user_default_photo.png',
                  width:width,
                  height: height,
                  fit: widget.boxfit,
                ),
          ),
        );
      }
    }
  }
}

class PsNetworkCircleImageForUser extends StatefulWidget {
  const PsNetworkCircleImageForUser(
      {Key? key,
        required this.photoKey,
        this.imagePath,
        this.asset,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.cover})
      : super(key: key);

  final double? width;
  final double? height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final String? imagePath;
  final String? asset;


  @override
  State<PsNetworkCircleImageForUser> createState() => _PsNetworkCircleImageForUserState();
}

class _PsNetworkCircleImageForUserState extends State<PsNetworkCircleImageForUser> {
  double? width;
  double? height;
  @override
  Widget build(BuildContext context) {
    if (widget.width == double.infinity) {
      width = MediaQuery.of(context).size.width;
    } else {
      width = widget.width;
    }
    if (widget.height == double.infinity) {
      height = MediaQuery.of(context).size.height;
    } else {
      height = widget.height;
    }
    if (widget.imagePath == null || widget.imagePath == '') {
      if (widget.asset == null || widget.asset == '') {
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Image.asset(
                  'assets/images/user_default_photo.png',
                  width: widget.width,
                  height: widget.height,
                  fit: widget.boxfit,
                )));
      } else {
        print('I Key : ${widget.photoKey}${widget.asset}');
        print('');
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: Hero(
              tag: '${widget.photoKey}${widget.asset}',
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: Image.asset(widget.asset!,
                    width: widget.width, height: widget.height, fit: widget.boxfit),
              ),
            ));
      }
    } else {
      final String fullImagePath = '${PsConfig.ps_app_image_url}${widget.imagePath}';
      final String thumbnailImagePath =
          '${PsConfig.ps_app_image_thumbs_url}${widget.imagePath}';

      if (widget.photoKey == '') {
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(10000.0),
              child: CachedNetworkImage(
                placeholder: (BuildContext context, String url) {
                  if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                    return CachedNetworkImage(
                      width: width,
                      height: height,
                      fit: widget.boxfit,
                      placeholder: (BuildContext context, String url) {
                        return PsSquareProgressWidget();
                      },
                      imageUrl: thumbnailImagePath,
                    );
                  } else {
                    return PsSquareProgressWidget();
                  }
                },
                width: width,
                height: height,
                fit: widget.boxfit,
                imageUrl: fullImagePath,
                errorWidget: (BuildContext context, String url, Object? error) =>
                    Image.asset(
                      'assets/images/user_default_photo.png',
                      width: width,
                      height: height,
                      fit: widget.boxfit,
                    ),
              ),
            ));
      } else {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: Hero(
              tag: '${widget.photoKey}${widget.imagePath}',
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: CachedNetworkImage(
                  placeholder: (BuildContext context, String url) {
                    if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                      return CachedNetworkImage(
                        width: width,
                        height: height,
                        fit: widget.boxfit,
                        placeholder: (BuildContext context, String url) {
                          return PsSquareProgressWidget();
                        },
                        imageUrl: thumbnailImagePath,
                      );
                    } else {
                      return PsSquareProgressWidget();
                    }
                  },
                  width:width,
                  height: height,
                  fit: widget.boxfit,
                  imageUrl: fullImagePath,
                  errorWidget:
                      (BuildContext context, String url, Object? error) =>
                      Image.asset(
                        'assets/images/user_default_photo.png',
                        width: width,
                        height: height,
                        fit: widget.boxfit,
                      ),
                ),
              )),
        );
      }
    }
  }
}

class PSProgressIndicator extends StatefulWidget {
  const PSProgressIndicator(this._status, {this.message});
  final PsStatus _status;
  final String? message;

  @override
  _PSProgressIndicator createState() => _PSProgressIndicator();
}

class _PSProgressIndicator extends State<PSProgressIndicator> {
  @override
  Widget build(BuildContext context) {
    if (widget._status == PsStatus.ERROR &&
        widget.message != null &&
        widget.message != '') {
      Fluttertoast.showToast(
          msg: widget.message!,
          toastLength: Toast.LENGTH_SHORT,
          gravity: ToastGravity.BOTTOM,
          timeInSecForIosWeb: 1,
          backgroundColor: Colors.redAccent,
          textColor: Colors.white);
    }
    return Padding(
      padding: const EdgeInsets.all(8.0),
      child: Align(
        alignment: Alignment.bottomCenter,
        child: Visibility(
          visible: widget._status == PsStatus.PROGRESS_LOADING,
          child: const LinearProgressIndicator(),
        ),
      ),
    );
  }
}

class PsNetworkCircleIconImage extends StatefulWidget {
  const PsNetworkCircleIconImage(
      {Key? key,
        required this.photoKey,
        required this.defaultIcon,
        this.width,
        this.height,
        this.onTap,
        this.boxfit = BoxFit.cover})
      : super(key: key);

  final double? width;
  final double? height;
  final Function? onTap;
  final String photoKey;
  final BoxFit boxfit;
  final DefaultIcon defaultIcon;


  @override
  State<PsNetworkCircleIconImage> createState() => _PsNetworkCircleIconImageState();
}

class _PsNetworkCircleIconImageState extends State<PsNetworkCircleIconImage> {
  double? width;
  double? height;
  @override
  Widget build(BuildContext context) {
    if (widget.width == double.infinity) {
      width = MediaQuery.of(context).size.width;
    } else {
      width = widget.width;
    }
    if (widget.height == double.infinity) {
      height = MediaQuery.of(context).size.height;
    } else {
      height = widget.height;
    }
    if (widget.defaultIcon.imgPath == '') {
      return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: ClipRRect(
              borderRadius: BorderRadius.circular(10000.0),
              child: Image.asset(
                'assets/images/placeholder_image.png',
                width: widget.width,
                height: widget.height,
                fit: widget.boxfit,
              )));
    } else {
      final String fullImagePath =
          '${PsConfig.ps_app_image_url}${widget.defaultIcon.imgPath}';
      final String thumbnailImagePath =
          '${PsConfig.ps_app_image_thumbs_url}${widget.defaultIcon.imgPath}';
      if (widget.photoKey == '') {
        return GestureDetector(
            onTap: widget.onTap as void Function()?,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(10000.0),
              child: CachedNetworkImage(
                placeholder: (BuildContext context, String url) {
                  if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                    return CachedNetworkImage(
                      width: width,
                      height: height,
                      fit: widget.boxfit,
                      placeholder: (BuildContext context, String url) {
                        return PsSquareProgressWidget();
                      },
                      imageUrl: thumbnailImagePath,
                    );
                  } else {
                    return PsSquareProgressWidget();
                  }
                },
                width: width,
                height: height,
                fit: widget.boxfit,
                imageUrl: fullImagePath,
                errorWidget: (BuildContext context, String url, Object? error) =>
                    Image.asset(
                      'assets/images/placeholder_image.png',
                      width: width,
                      height: height,
                      fit: widget.boxfit,
                    ),
              ),
            ));
      } else {
        return GestureDetector(
          onTap: widget.onTap as void Function()?,
          child: Hero(
              tag:
              '${widget.photoKey}${PsConfig.ps_app_image_url}${widget.defaultIcon.imgPath}',
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10000.0),
                child: CachedNetworkImage(
                  placeholder: (BuildContext context, String url) {
                    if (PsConfig.USE_THUMBNAIL_AS_PLACEHOLDER) {
                      return CachedNetworkImage(
                        width: width,
                        height:height,
                        fit: widget.boxfit,
                        placeholder: (BuildContext context, String url) {
                          return PsSquareProgressWidget();
                        },
                        imageUrl: thumbnailImagePath,
                      );
                    } else {
                      return PsSquareProgressWidget();
                    }
                  },
                  width: width,
                  height: height,
                  fit: widget.boxfit,
                  imageUrl: fullImagePath,
                  errorWidget:
                      (BuildContext context, String url, Object? error) =>
                      Image.asset(
                        'assets/images/placeholder_image.png',
                        width: width,
                        height: height,
                        fit: widget.boxfit,
                      ),
                ),
              )),
        );
      }
    }
  }
}
