@extends('layouts.landing')
@php
    $user = globalUserInfo();
    $roleID = $user->role_id;
@endphp
{{-- @dd($roleID) --}}

@section('content')
    <fieldset class="mb-6" style="background-image: url({{ asset('images/causlist.png') }})">
        <form class="form-inline" method="GET" id="landin_page_causelist_search_form">

  
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-5">
                        <input type="text" class="form-control form-control w-100" name="case_no">
                    </div>
                    
                    <div class="col-lg-2 text-left">
                        <button type="submit" class="btn btn-info font-weight-bolder mb-2 ml-2">অনুসন্ধান করুন</button>
                    </div>
                </div>
            </div>

        </form>

    </fieldset>
    <div class="card card-custom">
        <div class="card-header flex-wrap py-5">

            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title h2 font-weight-bolder">{{ $page_title }}</h3>
                    </div>
                    <div class="col-md-4">


                    </div>

                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="row" id="element-to-print">

                <div class="col-md-5 mx-auto">

                    <table class="tg">
                        @if (!empty($appeal))
                            <thead>
                                <tr>
                                    <th class="tg-5kbr" width="150">মামলার অবস্থা</th>
                                    <th class="tg-wo29">{{ appeal_status_bng($appeal->appeal_status) }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td class="tg-5kbr">মামলা নম্বর</td>
                                    <td class="tg-wo29">{{ en2bn($appeal->case_no) }}</td>
                                </tr>
                                <tr>
                                    <td class="tg-5kbr">আবেদনকারীর নাম</td>
                                    <td class="tg-wo29">{{ $appeal->caseCreator->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="tg-5kbr">আবেদনের তারিখ</td>
                                    <td class="tg-wo29">{{ en2bn($appeal->case_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="tg-5kbr">আবেদনকারীর ধরন</td>
                                    <td class="tg-wo29"></td>
                                </tr>


                            </tbody>
                        @endif
                    </table>

                </div>


                <div class="col-md-8 mx-auto" style="margin-top: 20px; text-align:center">
                    <h2>আদেশের তালিকা</h2>
                    <style type="text/css">
                        .tg2 {
                            border-collapse: collapse;
                            border-spacing: 0;
                            font-family: 'Kalpurush';
                            width: 100%;
                        }

                        .tg2 td {
                            border-color: black;
                            border-style: solid;
                            border-width: 1px;
                            font-family: 'Kalpurush', Arial, sans-serif;
                            font-size: 14px;
                            overflow: hidden;
                            padding: 6px 5px;
                            word-break: normal;
                        }

                        .tg2 th {
                            border-color: black;
                            border-style: solid;
                            border-width: 1px;
                            font-family: 'Kalpurush', Arial, sans-serif;
                            font-size: 14px;
                            font-weight: normal;
                            overflow: hidden;
                            padding: 6px 5px;
                            word-break: normal;
                        }

                        .tg2 .tg-5kbr {
                            background-color: #b9f5cc;
                            border-color: #c0c0c0;
                            font-weight: bold;
                            text-align: center;
                            vertical-align: top
                        }

                        .tg2 .tg-wo29 {
                            border-color: #c0c0c0;
                            text-align: left;
                            vertical-align: top
                        }

                    </style>
                    <table class="tg2">
                        <thead>
                            <tr>
                                <th class="tg-5kbr">নম্বর</th>
                                <th class="tg-5kbr">তারিখ</th>
                                <th class="tg-5kbr">আদেশ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shortOrderNameList as $key => $note)
                                @php
                                    $trialDate = date('Y-m-d',strtotime($note->conduct_date));
                                @endphp
                                <tr>
                                    <td class="tg-wo29">{{ en2bn($key+1) }} -
                                        নম্বর :</td>
                                    <td class="tg-wo29">{{ en2bn($trialDate) }}</td>
                                    <td class="tg-wo29">{{ $note->short_order_name }}</td>
                                </tr>
                            @empty
                                <td class="tg-wo29" colspan="3">তথ্য খুঁজে পাওয়া যায়নি... </td>
                            @endforelse
                        </tbody>
                        {{-- <tbody>
                            @forelse ($shortOrderTemplateList as $key => $shortOrderTemplate)
                                @php
                                    $trialDate = explode(' ', $shortOrderTemplate['conduct_date']);
                                @endphp
                                <tr>
                                    <td class="tg-wo29">{{ $shortOrderTemplate['index'] }} -
                                        নম্বর :</td>
                                    <td class="tg-wo29">{{ $trialDate[0] }}</td>
                                    <td class="tg-wo29">{{ $shortOrderTemplate['template_name'] }}</td>
                                </tr>
                            @empty
                                <td class="tg-wo29" colspan="3">তথ্য খুঁজে পাওয়া যায়নি... </td>
                            @endforelse
                        </tbody> --}}
                    </table>

                </div>

            </div>
            <br>

        </div>

    </div>
@endsection
