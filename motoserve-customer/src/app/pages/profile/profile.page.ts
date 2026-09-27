import {
  ChangeDetectorRef,
  Component,
  OnInit,
  inject,
  signal,
} from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonHeader,
  IonInput,
  IonModal,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import {
  AuthService,
  Customer,
} from '../../services/auth.service';
import { CartService } from '../../services/cart.service';
import { CustomerApiService } from '../../services/customer-api.service';
import { ConfirmDialogService } from '../../services/confirm-dialog.service';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [
    FormsModule,
    IonButton,
    IonCard,
    IonCardContent,
    IonContent,
    IonHeader,
    IonInput,
    IonModal,
    IonText,
    IonTitle,
    IonToolbar,
  ],
  template: `
    <ion-header>
      <ion-toolbar>
        <ion-title>My Profile</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content><main class="ms-shell ms-shell--narrow">
      <div class="ms-intro"><div><h1>My Profile</h1><p>Manage your account and security.</p></div></div>
      @if (errorMessage()) { <ion-text color="danger"><p class="ms-feedback">{{ errorMessage() }}</p></ion-text> }
      @if (successMessage()) { <ion-text color="success"><p class="ms-feedback">{{ successMessage() }}</p></ion-text> }
      @if (loading()) { <p class="ms-muted">Loading profile...</p> }
      @else if (customer(); as user) {
        <ion-card class="profile-card"><ion-card-content>
          <div class="profile-head"><div class="avatar" aria-label="Profile placeholder">{{ initials(user.name) }}</div><div><h2>{{ user.name }}</h2><span class="status">{{ user.status }}</span></div></div>
          <div class="detail"><span>Email address</span><strong>{{ user.email }}</strong></div>
          <div class="detail"><span>Phone number</span><strong>{{ user.phone || 'Not provided' }}</strong></div>
        </ion-card-content></ion-card>
        <h2 class="ms-section-title">Account settings</h2>
        <div class="settings-list">
          <button type="button" (click)="openEdit()"><span>Edit profile<small>Name, email and phone</small></span><b>›</b></button>
          <button type="button" (click)="openPassword()"><span>Change password<small>Protect your account</small></span><b>›</b></button>
        </div>
      }
      <div class="ms-actions footer-actions"><ion-button size="small" fill="outline" [disabled]="loading()" (click)="loadProfile()">Refresh</ion-button><ion-button size="small" fill="clear" color="danger" [disabled]="loggingOut()" (click)="logout()">{{ loggingOut() ? 'Logging out...' : 'Log out' }}</ion-button></div>
    </main></ion-content>
    <ion-modal class="ms-dialog" [isOpen]="editOpen()" (didDismiss)="editOpen.set(false)"><ng-template>
      <ion-header><ion-toolbar><ion-title>Edit Profile</ion-title><button class="ms-close" slot="end" type="button" aria-label="Close" (click)="editOpen.set(false)">×</button></ion-toolbar></ion-header>
      <ion-content class="ion-padding"><form class="ms-form" (ngSubmit)="saveProfile()">
        <ion-input label="Full name" labelPlacement="stacked" fill="outline" name="name" [(ngModel)]="form.name" maxlength="255" required></ion-input>
        <ion-input label="Email address" labelPlacement="stacked" fill="outline" type="email" name="email" [(ngModel)]="form.email" required></ion-input>
        <ion-input label="Phone number" labelPlacement="stacked" fill="outline" type="tel" name="phone" [(ngModel)]="form.phone" maxlength="20" required></ion-input>
        @if (formError()) { <ion-text color="danger"><p class="ms-feedback">{{ formError() }}</p></ion-text> }
        <div class="ms-form-actions"><ion-button size="small" fill="clear" type="button" (click)="editOpen.set(false)">Cancel</ion-button><ion-button size="small" type="submit" [disabled]="saving()">{{ saving() ? 'Saving...' : 'Save Changes' }}</ion-button></div>
      </form></ion-content>
    </ng-template></ion-modal>
    <ion-modal class="ms-dialog" [isOpen]="passwordOpen()" (didDismiss)="closePassword()"><ng-template>
      <ion-header><ion-toolbar><ion-title>Change Password</ion-title><button class="ms-close" slot="end" type="button" aria-label="Close" (click)="closePassword()">×</button></ion-toolbar></ion-header>
      <ion-content class="ion-padding"><form class="ms-form" (ngSubmit)="changePassword()">
        <ion-input label="Current password" labelPlacement="stacked" fill="outline" type="password" name="current" [(ngModel)]="currentPassword" required></ion-input>
        <ion-input label="New password" labelPlacement="stacked" fill="outline" type="password" name="new" [(ngModel)]="newPassword" minlength="8" required></ion-input>
        <ion-input label="Confirm new password" labelPlacement="stacked" fill="outline" type="password" name="confirm" [(ngModel)]="confirmPassword" required></ion-input>
        @if (formError()) { <ion-text color="danger"><p class="ms-feedback">{{ formError() }}</p></ion-text> }
        <div class="ms-form-actions"><ion-button size="small" fill="clear" type="button" (click)="closePassword()">Cancel</ion-button><ion-button size="small" type="submit" [disabled]="saving()">{{ saving() ? 'Updating...' : 'Update Password' }}</ion-button></div>
      </form></ion-content>
    </ng-template></ion-modal>
  `,
  styles: [`
    .profile-card { margin: 0; }
    .profile-card ion-card-content { padding: 22px; }
    .profile-head { display: flex; gap: 15px; align-items: center; margin-bottom: 24px; }
    .profile-head h2 { margin: 0 0 8px; font-size: 19px; }
    .avatar { flex-shrink: 0; display: grid; place-items: center; width: 64px; height: 64px; border-radius: 50%; color: white; background: linear-gradient(135deg,#658bfa,#986bf8); font-weight: 800; font-size: 22px; }
    .status { color: #a7ead0; background: #1c4e41; border-radius: 99px; padding: 4px 10px; text-transform: capitalize; font-size: 11px; }
    .detail { display: grid; gap: 4px; border-top: 1px solid #303a4c; padding: 13px 0; overflow-wrap: anywhere; }
    .detail span { color: #9caac2; font-size: 12px; }
    .detail strong { font-size: 14px; font-weight: 500; }
    .settings-list { overflow: hidden; border: 1px solid #2c374a; border-radius: 16px; }
    .settings-list button { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 14px 17px; border: 0; border-bottom: 1px solid #303a4c; background: #1a2130; text-align: left; color: #f4f6fb; font: inherit; cursor: pointer; }
    .settings-list button:last-child { border-bottom: 0; }
    .settings-list small { display: block; margin-top: 4px; color: #9caac2; font-size: 11px; }
    .settings-list b { font-size: 22px; color: #9caac2; }
    .footer-actions { justify-content: space-between; margin-top: 22px; }
  `],
})
export class ProfilePage implements OnInit {
  private api = inject(CustomerApiService);
  private confirmDialog = inject(ConfirmDialogService);
  private auth = inject(AuthService);
  private cart = inject(CartService);
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);

  customer = signal<Customer | null>(null);
  loading = signal(true);
  loggingOut = signal(false);
  saving = signal(false);
  editOpen = signal(false);
  passwordOpen = signal(false);
  successMessage = signal('');
  formError = signal('');
  errorMessage = signal('');

  form = { name: '', email: '', phone: '' };
  currentPassword = '';
  newPassword = '';
  confirmPassword = '';

  ngOnInit(): void {
    void this.loadProfile();
  }

  initials(name: string): string { return name.trim().split(/\s+/).slice(0, 2).map(part => part[0]?.toUpperCase() || '').join('') || 'MS'; }
  openEdit(): void {
    const user = this.customer();
    if (!user) return;
    this.form = { name: user.name, email: user.email, phone: user.phone || '' };
    this.formError.set(''); this.editOpen.set(true);
  }
  openPassword(): void { this.formError.set(''); this.passwordOpen.set(true); }
  closePassword(): void { this.passwordOpen.set(false); this.currentPassword = this.newPassword = this.confirmPassword = ''; }

  async saveProfile(): Promise<void> {
    if (this.saving()) return;
    const body = { name: this.form.name.trim(), email: this.form.email.trim(), phone: this.form.phone.trim() };
    if (!body.name || !body.email || !body.phone) { this.formError.set('Complete all profile fields.'); return; }
    this.saving.set(true); this.formError.set(''); this.successMessage.set('');
    try {
      const response = await firstValueFrom(this.api.patch<{ message: string; user: Customer }>('me', body));
      this.customer.set(response.user); this.editOpen.set(false); this.successMessage.set(response.message);
    } catch (error) { this.formError.set(this.describeError(error)); }
    finally { this.saving.set(false); this.cdr.markForCheck(); }
  }

  async changePassword(): Promise<void> {
    if (this.saving()) return;
    if (this.newPassword.length < 8) { this.formError.set('New password must have at least 8 characters.'); return; }
    if (this.newPassword !== this.confirmPassword) { this.formError.set('Passwords do not match.'); return; }
    this.saving.set(true); this.formError.set(''); this.successMessage.set('');
    try {
      const response = await firstValueFrom(this.api.put<{ message: string }>('me/password', {
        current_password: this.currentPassword, password: this.newPassword, password_confirmation: this.confirmPassword,
      }));
      this.closePassword(); this.successMessage.set(response.message);
    } catch (error) { this.formError.set(this.describeError(error)); }
    finally { this.saving.set(false); this.cdr.markForCheck(); }
  }

  async loadProfile(): Promise<void> {
    this.loading.set(true);
    this.errorMessage.set('');

    try {
      const user = await firstValueFrom(
        this.api.get<Customer>('me')
      );

      this.customer.set(user);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.loading.set(false);
      this.cdr.markForCheck();
    }
  }

  async logout(): Promise<void> {
    if (this.loggingOut()) return;

    const confirmed = await this.confirmDialog.confirm({
      header: 'Log out?',
      message: 'You will return to the MotoServe login page.',
      cancelText: 'Stay Logged In',
      confirmText: 'Log Out',
    });

    if (!confirmed) return;

    this.loggingOut.set(true);
    this.errorMessage.set('');

    try {
      await firstValueFrom(
        this.api.post<{ message: string }>('logout', {})
      );
    } catch (error) {
      // Kung expired na ang token, puwede pa ring i-clear
      // ang local session at bumalik sa Login.
      if (
        !(error instanceof HttpErrorResponse) ||
        error.status !== 401
      ) {
        this.errorMessage.set(this.describeError(error));
        this.loggingOut.set(false);
        this.cdr.markForCheck();
        return;
      }
    }

    try {
      await this.cart.clear();
      await this.auth.clearToken();

      await this.router.navigateByUrl('/login', {
        replaceUrl: true,
      });
    } catch {
      this.errorMessage.set('Could not clear the saved session.');
    } finally {
      this.loggingOut.set(false);
      this.cdr.markForCheck();
    }
  }

  private describeError(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      if (error.status === 0) {
        return 'Cannot connect to MotoServe API.';
      }

      if (error.status === 404) return 'Profile update API is not installed in Laravel yet.';
      const fields = error.error?.errors as Record<string, string[]> | undefined;
      if (fields) return Object.values(fields)[0]?.[0] ?? 'Invalid profile details.';

      return error.error?.message ??
        `Request failed (${error.status}).`;
    }

    return 'Something went wrong.';
  }
}
