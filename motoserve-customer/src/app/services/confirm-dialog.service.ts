import { inject, Injectable } from '@angular/core';
import { AlertController } from '@ionic/angular';

interface ConfirmOptions {
  header: string;
  message: string;
  confirmText: string;
  cancelText: string;
}

@Injectable({ providedIn: 'root' })
export class ConfirmDialogService {
  private alerts = inject(AlertController);

  async confirm(options: ConfirmOptions): Promise<boolean> {
    const alert = await this.alerts.create({
      cssClass: 'ms-confirm-alert',
      header: options.header,
      message: options.message,
      buttons: [
        { text: options.cancelText, role: 'cancel' },
        { text: options.confirmText, role: 'destructive' },
      ],
    });

    await alert.present();
    const { role } = await alert.onDidDismiss();
    return role === 'destructive';
  }
}
