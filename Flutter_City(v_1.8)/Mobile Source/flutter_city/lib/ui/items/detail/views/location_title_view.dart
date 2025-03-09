import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/ui/common/ps_expansion_tile.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/map_pin_intent_holder.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttericon/entypo_icons.dart';
import 'package:fluttericon/typicons_icons.dart';

class LocationTileView extends StatefulWidget {
  const LocationTileView({
    Key? key,
    required this.item,
  }) : super(key: key);

  final Item item;

  @override
  _LocationTileViewState createState() => _LocationTileViewState();
}

class _LocationTileViewState extends State<LocationTileView> {
  @override
  Widget build(BuildContext context) {
    final Widget _expansionTileTitleWidget = Text(
        Utils.getString('location_tile__title'),
        style: Theme.of(context).textTheme.subtitle1);

    final Widget _expansionTileLeadingWidget = Icon(
      Typicons.location,
      color: PsColors.mainColor,
    );
    // if (productDetail != null && productDetail.description != null) {
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
        leading: _expansionTileLeadingWidget,
        title: _expansionTileTitleWidget,
        children: <Widget>[
          Column(
            children: <Widget>[
              const Divider(
                height: PsDimens.space1,
              ),
              // const SizedBox(height: PsDimens.space16),
              Padding(
                padding: const EdgeInsets.all(PsDimens.space16),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Icon(
                      Entypo.address,
                      size: PsDimens.space20,
                      color: Theme.of(context).iconTheme.color,
                    ),
                    const SizedBox(width: PsDimens.space12),
                    Flexible(
                      flex: 1,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: <Widget>[
                          Text(
                            Utils.getString('item_detail__address'),
                            style: Theme.of(context).textTheme.bodyText1,
                          ),
                          const SizedBox(height: PsDimens.space12),
                          Text(
                            widget.item.address!,
                            style: Theme.of(context).textTheme.bodyText1,
                            maxLines: 2,
                            textAlign: TextAlign.justify,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),


              // const Divider(
              //   height: PsDimens.space1,
              // ),
              InkWell(
                child: Ink(
                  child: Padding(
                    padding: const EdgeInsets.only(bottom: PsDimens.space16),
                    child: Text(
                      Utils.getString('location_tile__view_on_map_button')
                          .toUpperCase(),
                      style: Theme.of(context)
                          .textTheme
                          .bodyText1!
                          .copyWith(color: PsColors.mainColor),
                    ),
                  ),
                ),
                onTap: () async {
                  if (PsConfig.isUseGoogleMap) {
                    await Navigator.pushNamed(context, RoutePaths.googleMapPin,
                        arguments: MapPinIntentHolder(
                            flag: PsConst.VIEW_MAP,
                            mapLat: widget.item.lat,
                            mapLng: widget.item.lng));
                  } else {
                    await Navigator.pushNamed(context, RoutePaths.mapPin,
                        arguments: MapPinIntentHolder(
                            flag: PsConst.VIEW_MAP,
                            mapLat: widget.item.lat,
                            mapLng: widget.item.lng));
                  }
                },
              ),
            ],
          ),
        ],
      ),
    );
    // } else {
    //   return const Card();
    // }
  }
}
