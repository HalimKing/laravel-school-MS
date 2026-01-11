<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->reference_no ?? 'FEE-'.$payment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --accent: #3b82f6;
            --success: #10b981;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        .receipt-wrapper {
            max-width: 850px;
            margin: 0 auto;
        }

        .action-bar {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-modern {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-print {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .btn-close {
            background: rgba(255,255,255,0.2);
            color: white;
            backdrop-filter: blur(10px);
        }

        .receipt-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .receipt-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .receipt-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .school-info h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .school-info p {
            opacity: 0.9;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        .receipt-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .receipt-number {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .receipt-date {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .receipt-body {
            padding-left: 40px;
            padding-right: 40px;
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .info-section {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 10px 25px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .info-value {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .info-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .payment-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 10px;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .payment-table thead {
            background: var(--bg-light);
        }

        .payment-table th {
            padding: 10px 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            text-align: left;
        }

        .payment-table td {
            padding: 25px 20px;
            border-top: 1px solid var(--border);
        }

        .fee-description {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .payment-method {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dbeafe;
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        .remarks {
            margin-top: 12px;
            padding: 12px;
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #92400e;
        }

        .amount-cell {
            text-align: right;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .total-section {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border-radius: 12px;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border: 2px solid var(--primary);
        }

        .total-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .total-amount {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
        }

        .amount-words {
            background: white;
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }

        .amount-words-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            font-weight: 600;
        }

        .amount-words-line {
            font-style: italic;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            padding-bottom: 5px;
            min-height: 24px;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid var(--border);
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid var(--text-dark);
            margin: 0 auto 10px;
            width: 200px;
        }

        .signature-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .receipt-footer {
            background: var(--bg-light);
            padding: 10px;
            text-align: center;
            border-top: 1px solid var(--border);
        }

        .footer-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .footer-text {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        @media print {
            body { 
                background: white;
                padding: 0;
            }
            .receipt-wrapper {
                max-width: 100%;
            }
            .action-bar { 
                display: none;
            }
            .receipt-card {
                box-shadow: none;
                border-radius: 0;
            }
        }

        @media (max-width: 768px) {
            .receipt-body {
                padding: 25px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
            .signature-section {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .total-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">
    <div class="action-bar">
        <button onclick="window.print()" class="btn-modern btn-print">
            🖨️ Print Receipt
        </button>
        <button onclick="window.close()" class="btn-modern btn-close">
            Close
        </button>
    </div>

    <div class="receipt-card">
        <div class="receipt-header">
            <div class="header-content">
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td width="60%" valign="top">
                            <div class="school-info">
                                <h1>{{ config('app.name', 'Your School Name') }}</h1>
                                <p>
                                    123 Education Lane, Accra, Ghana<br>
                                    📞 +233 24 000 0000 | ✉️ info@school.edu
                                </p>
                            </div>
                        </td>
                        <td width="40%" valign="top" align="right" style="padding-top: 10px;">
                            <div class="receipt-badge text-dark" style="display: inline-block; background: #f0f0f0; padding: 5px 10px; border-radius: 4px; font-weight: bold; margin-bottom: 5px;">
                                Official Receipt
                            </div>
                            <div class="receipt-number" style="margin-bottom: 5px;">
                                No: {{ $payment->reference_no ?? 'REC-'.str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="receipt-date" style="color: #666;">
                                📅 {{ $payment->payment_date->format('d M, Y') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="receipt-body">
            <div class="info-section">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin: 10px 0; background: #f9f9f9; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td width="50%" style="padding: 15px; border-right: 1px solid #e0e0e0; vertical-align: top;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 0 0 5px 0;">
                                        <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Student Name
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 8px 0;">
                                        <div style="font-size: 16px; font-weight: bold; color: #2c3e50;">
                                            {{ $payment->levelData->student->first_name }} {{ $payment->levelData->student->last_name }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0;">
                                        <div style="font-size: 13px; color: #777;">
                                            Student ID: {{ $payment->levelData->student->student_id }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" style="padding: 15px; vertical-align: top;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 0 0 5px 0;">
                                        <div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Class / Session
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 8px 0;">
                                        <div style="font-size: 16px; font-weight: bold; color: #2c3e50;">
                                            {{ $payment->levelData->class->name }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0;">
                                        <div style="font-size: 13px; color: #777;">
                                            Academic Year: {{ $payment->levelData->academicYear->name }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Payment Description</th>
                        <th>Payment Method</th>
                        <th style="text-align: right; width: 180px;">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fee-description">{{ $payment->fee->feeCategory->name }}</div>
                        </td>
                        <td>
                            <div class="payment-method">
                                💳 {{ ucfirst($payment->payment_method) }}
                            </div>
                        </td>
                        <td class="amount-cell">₵{{ number_format($payment->amount_paid, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Accounts Officer</div>
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <div class="footer-text">
                <strong>This is a computer-generated receipt. No signature required.</strong><br>
                Thank you for your prompt payment. For inquiries, please contact our accounts department.
            </div>
        </div>
    </div>
</div>

</body>
</html>