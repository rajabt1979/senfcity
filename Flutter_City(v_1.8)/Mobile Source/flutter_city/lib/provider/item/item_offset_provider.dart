import 'package:flutter/foundation.dart';

class ItemOffsetProvider extends ChangeNotifier {
  double containerOffset = 0;
  double _delta = 0, _oldOffset = 0;
  void offsetListener(double offset, double containerHeight) {
    _delta += offset - _oldOffset;

    if (_delta > containerHeight)
      _delta = containerHeight;
    else if (_delta < 0) {
      _delta = 0;
    }
    _oldOffset = offset;
    containerOffset = -_delta;
    notifyListeners();
  }
}
