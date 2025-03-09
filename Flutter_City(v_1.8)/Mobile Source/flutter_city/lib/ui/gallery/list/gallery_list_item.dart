import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/viewobject/default_photo.dart';

class GalleryListItem extends StatelessWidget {
  const GalleryListItem({
    Key? key,
    required this.image,
    this.animationController,
    this.animation,
    this.onImageTap,
    this.deleteIconTap,
  }) : super(key: key);

  final DefaultPhoto? image;
  final Function? onImageTap;
  final Function? deleteIconTap;
  final AnimationController? animationController;
  final Animation<double>? animation;

  @override
  Widget build(BuildContext context) {
    animationController!.forward();
    final Widget _imageWidget = PsNetworkImage(
      photoKey: '',
      defaultPhoto: image!,
      width: MediaQuery.of(context).size.width,
      height: PsDimens.space120,
      boxfit: BoxFit.cover,
      onTap: onImageTap,
    );
    return AnimatedBuilder(
        animation: animationController!,
        child: Stack(
          children: <Widget>[
            Container(
              margin: const EdgeInsets.all(PsDimens.space4),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(PsDimens.space8),
                child: image != null ? _imageWidget : null,
              ),
            ),
            Positioned(
              right: PsDimens.space12,
              bottom: PsDimens.space12,
              child: InkWell(
                onTap: deleteIconTap as void Function()?,
                child: Icon(
                  Icons.delete,
                  size: PsDimens.space32,
                  color: PsColors.mainColor,
                ),
              ),
            ),
          ],
        ),
        builder: (BuildContext context, Widget? child) {
          return FadeTransition(
              opacity: animation!,
              child: Transform(
                transform: Matrix4.translationValues(
                    0.0, 100 * (1.0 - animation!.value), 0.0),
                child: child,
              ));
        });
  }
}
