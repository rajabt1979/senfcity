import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/ps_expansion_tile.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttericon/font_awesome_icons.dart';

class CategoryTileView extends StatelessWidget {
  const CategoryTileView({
    Key? key,
    required this.itemData,
  }) : super(key: key);

  final Item itemData;
  @override
  Widget build(BuildContext context) {
    final Widget _expansionTileTitleWidget =
        Text('دسته بندی', style: Theme.of(context).textTheme.subtitle1);

    final Widget _expansionTileLeadingIconWidget = Icon(
      FontAwesome.street_view,
      color: PsColors.mainColor,
    );
    return Container(
      margin: const EdgeInsets.only(
          left: PsDimens.space12,
          right: PsDimens.space12,
          bottom: PsDimens.space12),
      decoration: BoxDecoration(
        border: Border.all(color: PsColors.grey, width: 0.3),
        color: PsColors.backgroundColor,
        borderRadius: const BorderRadius.all(Radius.circular(PsDimens.space8)),
      ),
      child: PsExpansionTile(
        initiallyExpanded: true,
        leading: _expansionTileLeadingIconWidget,
        title: _expansionTileTitleWidget,
        children: <Widget>[
          Column(
            children: <Widget>[
              const Divider(
                height: PsDimens.space1,
              ),
              Padding(
                padding: const EdgeInsets.all(PsDimens.space16),
                child: Align(
                  alignment: Alignment.centerLeft,
                  child: Text(
                      '${itemData.category!.name} / ${itemData.subCategory!.name}',
                      textAlign: TextAlign.start,
                      style: Theme.of(context).textTheme.bodyText1),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
