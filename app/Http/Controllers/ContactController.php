<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom'     => 'required|string|max:100',
            'prenom'  => 'required|string|max:100',
            'email'   => 'required|email',
            'sujet'   => 'required|string',
            'message' => 'required|string|max:1000',
        ], [
            'nom.required'     => 'Le nom est obligatoire.',
            'prenom.required'  => 'Le prénom est obligatoire.',
            'email.required'   => 'L\'email est obligatoire.',
            'email.email'      => 'L\'email n\'est pas valide.',
            'sujet.required'   => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
        ]);

        // Envoi du mail à contactstagelink@gmail.com
        Mail::send([], [], function ($mail) use ($request) {
            $mail->to('contactstagelink@gmail.com')
                 ->replyTo($request->email, $request->prenom . ' ' . $request->nom)
                 ->subject('[StageLink Contact] ' . ucfirst($request->sujet) . ' — ' . $request->prenom . ' ' . $request->nom)
                 ->html('
                    <div style="font-family: Poppins, Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <div style="background: linear-gradient(135deg, #0A1F44, #1E88FF); padding: 30px; border-radius: 16px 16px 0 0; color: white;">
                            <h2 style="margin: 0; font-size: 22px;">📬 Nouveau message de contact</h2>
                            <p style="margin: 8px 0 0; opacity: .85; font-size: 14px;">Via le formulaire StageLink</p>
                        </div>
                        <div style="background: white; padding: 30px; border: 1px solid #E8EEF7;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px 0; font-weight: 700; color: #0A1F44; width: 120px; font-size: 14px;">Nom</td>
                                    <td style="padding: 10px 0; color: #64748B; font-size: 14px;">' . $request->prenom . ' ' . $request->nom . '</td>
                                </tr>
                                <tr style="border-top: 1px solid #F1F5F9;">
                                    <td style="padding: 10px 0; font-weight: 700; color: #0A1F44; font-size: 14px;">Email</td>
                                    <td style="padding: 10px 0; font-size: 14px;"><a href="mailto:' . $request->email . '" style="color: #1E88FF;">' . $request->email . '</a></td>
                                </tr>
                                <tr style="border-top: 1px solid #F1F5F9;">
                                    <td style="padding: 10px 0; font-weight: 700; color: #0A1F44; font-size: 14px;">Sujet</td>
                                    <td style="padding: 10px 0; color: #64748B; font-size: 14px;">' . ucfirst($request->sujet) . '</td>
                                </tr>
                            </table>
                            <div style="margin-top: 20px; padding: 20px; background: #F8FAFC; border-radius: 12px; border-left: 4px solid #1E88FF;">
                                <p style="font-weight: 700; color: #0A1F44; margin: 0 0 10px; font-size: 14px;">Message :</p>
                                <p style="color: #64748B; line-height: 1.7; margin: 0; font-size: 14px;">' . nl2br(e($request->message)) . '</p>
                            </div>
                        </div>
                        <div style="background: #F8FAFC; padding: 20px; border-radius: 0 0 16px 16px; text-align: center; border: 1px solid #E8EEF7; border-top: none;">
                            <p style="color: #94A3B8; font-size: 12px; margin: 0;">© 2026 StageLink — Formulaire de contact</p>
                        </div>
                    </div>
                 ');
        });

        // Redirection avec message de succès
        return redirect()->route('contact')
                         ->with('success', '✅ Message envoyé ! Nous vous répondrons sous 24h.');
    }
}