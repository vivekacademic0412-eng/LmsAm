@extends('layouts.app')

@section('title', 'Payment Transactions | Academic Mantra Services')

@section('meta_description', 'View your payment transactions, invoices, and payment status for Academic Mantra Services. Track successful, pending, and failed payments securely from your account.')

@section('meta_keywords', 'payment transactions, payment history, online payment, Razorpay payment, payment status, invoices, Academic Mantra Services, LMS payment, course payment, transaction details')

@section('content')
<livewire:payment-transactions/>
@endsection