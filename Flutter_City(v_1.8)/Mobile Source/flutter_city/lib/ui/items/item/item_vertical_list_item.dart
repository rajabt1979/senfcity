import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/ui/common/smooth_star_rating_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/item.dart';

class ItemVeticalListItem extends StatelessWidget {
  const ItemVeticalListItem(
      {Key? key,
      required this.item,
      this.onTap,
      this.animationController,
      this.animation,
      this.coreTagKey})
      : super(key: key);

  final Item item;
  final Function? onTap;
  final String ?coreTagKey;
  final AnimationController? animationController;
  final Animation<double>? animation;

  @override
  Widget build(BuildContext context) {
    animationController!.forward();
    return AnimatedBuilder(
        animation: animationController!,
        child: GestureDetector(
            onTap: onTap as void Function()?,
            child: GridTile(
              header: Container(
                padding: const EdgeInsets.all(PsDimens.space8),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Padding(
                      padding: const EdgeInsets.only(
                          left: PsDimens.space8, top: PsDimens.space8),
                      child: Align(
                          alignment: Alignment.topRight,
                          child: item.isFeatured == PsConst.ONE
                              ? Image.asset(
                                  'assets/images/baseline_feature_circle_24.png',
                                  width: PsDimens.space32,
                                  height: PsDimens.space32,
                                )
                              : Container()),
                    )
                  ],
                ),
              ),
              child: Container(
                margin: const EdgeInsets.symmetric(
                    horizontal: PsDimens.space8, vertical: PsDimens.space8),
                decoration: BoxDecoration(
                  color: PsColors.backgroundColor,
                  borderRadius:
                      const BorderRadius.all(Radius.circular(PsDimens.space8)),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.max,
                  mainAxisAlignment: MainAxisAlignment.end,
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: <Widget>[
                    Expanded(
                      child: Container(
                        decoration: const BoxDecoration(
                          borderRadius: BorderRadius.all(
                              Radius.circular(PsDimens.space8)),
                        ),
                        child: ClipPath(
                          child: PsNetworkImage(
                            photoKey: '',
                            defaultPhoto: item.defaultPhoto!,
                            width: PsDimens.space180,
                            height: double.infinity,
                            boxfit: BoxFit.cover,
                            onTap: () {
                              Utils.psPrint(item.defaultPhoto!.imgParentId!);
                              onTap!();
                            },
                          ),
                          clipper: const ShapeBorderClipper(
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.all(
                                      Radius.circular(PsDimens.space8)))),
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(
                          left: PsDimens.space8,
                          top: PsDimens.space16,
                          right: PsDimens.space8,
                          bottom: PsDimens.space12),
                      child: Text(
                        item.name!,
                        overflow: TextOverflow.ellipsis,
                        style: Theme.of(context).textTheme.bodyText1,
                        maxLines: 1,
                      ),
                      // ),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(
                          left: PsDimens.space8,
                          top: PsDimens.space12,
                          right: PsDimens.space8,
                          bottom: PsDimens.space4),
                      child: Text(
                        item.description!,
                        overflow: TextOverflow.ellipsis,
                        style: Theme.of(context).textTheme.bodyText1,
                        maxLines: 1,
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(
                          left: PsDimens.space8,
                          top: PsDimens.space8,
                          right: PsDimens.space4),
                      child: SmoothStarRating(
                          key: Key(item.ratingDetail!.totalRatingValue!),
                          rating:
                              double.parse(item.ratingDetail!.totalRatingValue!),
                          allowHalfRating: false,
                          onRated: (double? v) {
                            onTap!();
                          },
                          starCount: 5,
                          size: 20.0,
                          color: PsColors.ratingColor,
                          borderColor: Utils.isLightMode(context)
                              ? PsColors.black.withAlpha(100)
                              : PsColors.white,
                          spacing: 0.0),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(
                          top: PsDimens.space4,
                          bottom: PsDimens.space12,
                          left: PsDimens.space12,
                          right: PsDimens.space12),
                      child: Row(
                        children: <Widget>[
                          Text(
                              '${item.ratingDetail!.totalRatingValue} ${Utils.getString('feature_slider__rating')}',
                              textAlign: TextAlign.start,
                              style: Theme.of(context).textTheme.caption),
                          Expanded(
                            child: Text(
                                '( ${item.ratingDetail!.totalRatingCount} ${Utils.getString('feature_slider__reviewer')} )',
                                textAlign: TextAlign.start,
                                style: Theme.of(context).textTheme.caption),
                          )
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            )),
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
