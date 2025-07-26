// Vercel Serverless Function untuk Email Notification
// File: /api/send-email.js
// Equivalent dari send-notification-phpmailer.php

import nodemailer from 'nodemailer';

export default async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle preflight OPTIONS request
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  // Only accept POST requests
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    // Get form data from request body
    const {
      to_email = 'teguhgunawan21@gmail.com',
      from_name = '',
      from_email = '',
      subject = 'PENDAFTARAN WEBINAR MENTORING UMROH',
      nama = '',
      whatsapp = '',
      email = '',
      sapaan = '',
      asal_kota = '',
      status = '',
      agent_id = '',
      waktu_daftar = new Date().toLocaleString('id-ID')
    } = req.body;

    // Validate required fields
    if (!nama || !whatsapp) {
      throw new Error('Missing required fields: nama and whatsapp');
    }

    // Create Nodemailer transporter (SMTP config)
    const transporter = nodemailer.createTransporter({
      host: 'srv142.niagahoster.com',
      port: 465,
      secure: true, // SSL
      auth: {
        user: 'admin@pejuangumroh.id',
        pass: 'peeweegaskins' // Password email
      },
      // Additional options for problematic servers
      tls: {
        rejectUnauthorized: false
      }
    });

    // Email content
    const emailContent = `PENDAFTARAN WEBINAR BARU!

Detail Pendaftar:
====================
Nama: ${nama}
WhatsApp: ${whatsapp}
Email: ${email}
Sapaan: ${sapaan}
Asal Kota: ${asal_kota}
Status: ${status}

Agent ID: ${agent_id}
Produk Terdaftar: 10 produk umroh

Waktu Daftar: ${waktu_daftar}

Terima kasih!
====================`;

    // Email options
    const mailOptions = {
      from: '"Mentoring Umroh" <admin@pejuangumroh.id>',
      to: to_email,
      replyTo: email || 'admin@pejuangumroh.id',
      subject: subject,
      text: emailContent
    };

    // Send email
    const info = await transporter.sendMail(mailOptions);

    console.log('✅ Email sent successfully:', info.messageId);

    // Return success response
    return res.status(200).json({
      success: true,
      message: 'Email notification sent successfully via Nodemailer',
      messageId: info.messageId,
      to: to_email,
      subject: subject,
      method: 'Vercel Serverless Function + Nodemailer'
    });

  } catch (error) {
    console.error('❌ Email error:', error.message);

    // Return error response
    return res.status(500).json({
      success: false,
      error: error.message,
      method: 'Vercel Serverless Function + Nodemailer',
      smtp_config: {
        host: 'srv142.niagahoster.com',
        port: 465,
        username: 'admin@pejuangumroh.id'
      }
    });
  }
} 