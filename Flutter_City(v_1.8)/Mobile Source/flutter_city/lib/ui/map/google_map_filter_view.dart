import 'dart:math';
import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/base/ps_widget_with_appbar_with_no_provider.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
//import 'package:geocode/geocode.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:provider/provider.dart';

class GoogleMapFilterView extends StatefulWidget {
  const GoogleMapFilterView({required this.itemParameterHolder});

  final ItemParameterHolder itemParameterHolder;

  @override
  _MapFilterViewState createState() => _MapFilterViewState();
}

class _MapFilterViewState extends State<GoogleMapFilterView>
    with TickerProviderStateMixin {
  List<String> seekBarValues = <String>[
    '0.5',
    '1',
    '2.5',
    '5',
    '10',
    '25',
    '50',
    '100',
    '200',
    '500',
    'All'
  ];
  LatLng? latlng;
  final double zoom = 10;
  double radius = -1;
  double defaultRadius = 3000;
  bool isRemoveCircle = false;
  String address = '';
  bool isFirst = true;
  CameraPosition? kGooglePlex;
  GoogleMapController? mapController;
  PsValueHolder ?valueHolder;

  dynamic loadAddress() async {
    // try {
    //   final Address? locationAddress = await GeoCode().reverseGeocoding(
    //       latitude: latlng!.latitude, longitude: latlng!.longitude);
    //
    //   if (locationAddress != null) {
    //     if (locationAddress.streetAddress != null &&
    //         locationAddress.countryName != null) {
    //       address =
    //       '${locationAddress.streetAddress} \n, ${locationAddress.countryName}';
    //     } else {
    //       address = locationAddress.region!;
    //     }
    //   }
    // } catch (e) {
    //   address = '';
    //   print(e);
    // }
  }

  double _value = 0.3;
  String kmValue = '5';

  int findTheIndexOfTheValue(String value) {
    int index = 0;

    for (int i = 0; i < seekBarValues.length - 1; i++) {
      if (!(value == 'All')) {
        if (getMiles(seekBarValues[i]) == value) {
          index = i;
          break;
        }
      } else {
        index = seekBarValues.length - 1;
      }
    }

    return index;
  }

  String getMiles(String kmValue) {
    final double _km = double.parse(kmValue);
    return (_km * 0.621371).toStringAsFixed(3);
  }

  @override
  Widget build(BuildContext context) {
    valueHolder = Provider.of<PsValueHolder?>(context, listen: false);

    if (widget.itemParameterHolder.lat! == '')
      latlng = const LatLng(0.0, 0.0);
    else
      latlng ??= LatLng(double.parse(widget.itemParameterHolder.lat ?? ''),
          double.parse(widget.itemParameterHolder.lng ?? ''));

    if (widget.itemParameterHolder.miles != '' && isFirst) {
      final int _index =
      findTheIndexOfTheValue(widget.itemParameterHolder.miles!);
      kmValue = seekBarValues[_index];
      final double _val = double.parse(getMiles(kmValue)) * 1000;
      radius = _val;
      defaultRadius = radius;
      _value = _index / 10;
      isFirst = false;
    }

    final double scale = defaultRadius / 300; //radius/20
    final double value = 16 - log(scale) / log(2);
    loadAddress();

    kGooglePlex = CameraPosition(
      target: LatLng(double.parse(widget.itemParameterHolder.lat!),
          double.parse(widget.itemParameterHolder.lng!)),
      zoom: value,
    );

    print('value $value');

    return PsWidgetWithAppBarWithNoProvider(
        appBarTitle: Utils.getString('map_filter__title'),
        actions: <Widget>[
          InkWell(
            child: Center(
              child: Text(
                Utils.getString('map_filter__reset'),
                textAlign: TextAlign.justify,
                style: Theme.of(context)
                    .textTheme
                    .bodyMedium!
                    .copyWith(fontWeight: FontWeight.bold)
                    .copyWith(color: PsColors.mainColorWithWhite),
              ),
            ),
            onTap: () {
              setState(() {
                isRemoveCircle = true;
                _value = 1.0;
              });
            },
          ),
          const SizedBox(
            width: PsDimens.space20,
          ),
          InkWell(
            child: Center(
              child: Text(
                Utils.getString('map_filter__apply'),
                textAlign: TextAlign.center,
                style: Theme.of(context)
                    .textTheme
                    .bodyMedium!
                    .copyWith(fontWeight: FontWeight.bold)
                    .copyWith(color: PsColors.mainColorWithWhite),
              ),
            ),
            onTap: () {
              if (kmValue == 'All') {
                widget.itemParameterHolder.lat = '';
                widget.itemParameterHolder.lng = '';
                widget.itemParameterHolder.miles = '';
              } else {
                widget.itemParameterHolder.lat = latlng!.latitude.toString();
                widget.itemParameterHolder.lng = latlng!.longitude.toString();
                widget.itemParameterHolder.miles = getMiles(kmValue);
              }
              Navigator.pop(context, widget.itemParameterHolder);
            },
          ),
          const SizedBox(
            width: PsDimens.space16,
          ),
        ],
        child: Scaffold(
          body: Column(
            children: <Widget>[
              Flexible(
                child: GoogleMap(
                    onMapCreated: _onMapCreated,
                    initialCameraPosition: kGooglePlex!,
                    circles: <Circle>{}..add(Circle(
                      circleId: CircleId(address),
                      center: latlng!,
                      radius: isRemoveCircle == true
                          ? 0.0
                          : radius <= 0.0
                          ? defaultRadius
                          : radius,
                      fillColor: Colors.blue.withOpacity(0.7),
                      strokeWidth: 3,
                      strokeColor: Colors.redAccent,
                    )),
                    onTap: _handleTap),
              ),
              Padding(
                padding: const EdgeInsets.only(
                  left: 16,
                  right: 16,
                  top: PsDimens.space8,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: <Widget>[
                    Text(
                      Utils.getString('map_filter__browsing'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyMedium!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                    Text(
                      Utils.getString('map_filter__all'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyMedium!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              Container(
                child: Slider(
                  value: _value,
                  onChanged: (double newValue) {
                    setState(() {
                      _value = newValue;
                      kmValue = seekBarValues[(_value * 10).toInt()];
                      if (kmValue == 'All') {
                        isRemoveCircle = true;
                      } else {
                        radius = double.parse(getMiles(kmValue)) *
                            1000; //_value * 10000;
                      }
                      _value == 1
                          ? isRemoveCircle = true
                          : isRemoveCircle = false;
                      defaultRadius != 0
                          ? defaultRadius = 500
                          : defaultRadius = 500;
                    });
                  },
                  divisions: 10,
                  label: _value == 1
                      ? seekBarValues[(_value * 10).toInt()]
                      : seekBarValues[(_value * 10).toInt()] +
                      Utils.getString('map_filter__km'),
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(
                  left: 16,
                  right: 16,
                  bottom: PsDimens.space8,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: <Widget>[
                    Text(
                      Utils.getString('map_filter__lowest_km'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyMedium!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                    Text(
                      Utils.getString('map_filter__all'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyMedium!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ));
  }

  void _onMapCreated(GoogleMapController controller) {
    mapController = controller;
  }

  void _handleTap(LatLng latlng) {
    setState(() {
      this.latlng = latlng;
    });
  }
}
