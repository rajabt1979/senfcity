import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/viewobject/AttributeDetail.dart';
import 'package:fluttercity/viewobject/item.dart';

class AttributeDetailListItem extends StatelessWidget {
  const AttributeDetailListItem(
      {Key? key,
      required this.attributeDetail,
      required this.product,
      this.onTap,
      this.animationController,
      this.animation})
      : super(key: key);

  final AttributeDetail attributeDetail;
  final Item product;
  final Function? onTap;
  final AnimationController? animationController;
  final Animation<double>? animation;

  @override
  Widget build(BuildContext context) {
    animationController!.forward();

    return AnimatedBuilder(
      animation: animationController!,
      child: GestureDetector(
        onTap: onTap as void Function()?,
        child: Container(
          color: PsColors.backgroundColor,
          width: MediaQuery.of(context).size.width,
          margin: const EdgeInsets.only(top: PsDimens.space8),
          child: Padding(
            padding: const EdgeInsets.all(PsDimens.space16),
            child: Text(
              attributeDetail.name == ''
                  ? ''
                  : '${attributeDetail.name} [ +${product.currencySymbol}${attributeDetail.additionalPrice} ]',
              textAlign: TextAlign.start,
              style: Theme.of(context)
                  .textTheme
                  .subtitle2!
                  .copyWith(fontWeight: FontWeight.bold),
            ),
          ),
        ),
      ),
      builder: (BuildContext contenxt, Widget? child) {
        return FadeTransition(
          opacity: animation!,
          child: Transform(
            transform: Matrix4.translationValues(
                0.0, 100 * (1.0 - animation!.value), 0.0),
            child: child,
          ),
        );
      },
    );
  }
}
