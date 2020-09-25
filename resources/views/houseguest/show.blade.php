@extends('layouts.app')

@section('content')
    <div class="leaderboard-wrap">
        <h3 class="mg-btm-lg">{{ $houseguest->nickname }}</h3>

        <projection-item
            :houseguest="{{$houseguest}}"
            :linktohg="false"
        ></projection-item>

        <table>
        </table>
    </div>
@endsection
