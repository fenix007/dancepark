<?php
namespace DancePark\CommonBundle\EventListener\Event;

final class CommonEvents
{
    const ADDRESS_GROUP_PRE_REMOVE = 'address_group.pre_persist';
    const ADDRESS_LEVEL_PRE_REMOVE = 'address_level.pre_remove';
    const ADDRESS_REGION_PRE_REMOVE = 'address_region.pre_remove';
    const DANCE_TYPE_PRE_REMOVE = 'dance_type.pre_remove';
    const DANCE_GROUP_PRE_REMOVE = 'dance_group.pre_remove';
    const METRO_STATION_PRE_REMOVE = 'metro_station.pre_remove';
    const PLACE_PRE_REMOVE = 'place.pre_remove';
    const PLACE_PRE_PERSIST = 'place.pre_persist';
    const PLACE_PRE_UPDATE = 'place.pre_update';
    const PRE_PERSIST = 'pre_persist';
    const PRE_UPDATE = 'pre_update';
}