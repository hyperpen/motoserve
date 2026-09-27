import { ChangeDetectorRef, Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import {
  IonButton,
  IonContent,
  IonInput,
  IonText,
} from '@ionic/angular';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [FormsModule, IonButton, IonContent, IonInput, IonText],
  template: `
    <ion-content>
      <div class="page">
        <div class="auth-box">
          <div class="logo">M</div>
          <h1>MotoServe</h1>
          <p class="subtitle">Create your customer account</p>

          <form (ngSubmit)="submit()">
            <ion-input
              label="Full Name"
              labelPlacement="stacked"
              fill="outline"
              type="text"
              autocomplete="name"
              name="name"
              [(ngModel)]="name"
              required>
            </ion-input>

            <ion-input
              label="Email"
              labelPlacement="stacked"
              fill="outline"
              type="email"
              inputmode="email"
              autocomplete="email"
              name="email"
              [(ngModel)]="email"
              required>
            </ion-input>

            <ion-input
              label="Phone Number"
              labelPlacement="stacked"
              fill="outline"
              type="tel"
              inputmode="tel"
              autocomplete="tel"
              name="phone"
              [(ngModel)]="phone"
              required>
            </ion-input>

            <ion-input
              label="Password"
              labelPlacement="stacked"
              fill="outline"
              type="password"
              autocomplete="new-password"
              name="password"
              [(ngModel)]="password"
              required>
            </ion-input>

            <ion-input
              label="Confirm Password"
              labelPlacement="stacked"
              fill="outline"
              type="password"
              autocomplete="new-password"
              name="confirmation"
              [(ngModel)]="confirmation"
              required>
            </ion-input>

            @if (errorMessage) {
              <ion-text color="danger">
                <p class="error">{{ errorMessage }}</p>
              </ion-text>
            }

            <ion-button
              class="main-button"
              expand="block"
              type="submit"
              [disabled]="loading">
              {{ loading ? 'Creating account...' : 'Register' }}
            </ion-button>
          </form>

          <div class="footer-link">
            <span>Already have an account?</span>
            <ion-button
              fill="clear"
              size="small"
              type="button"
              [disabled]="loading"
              (click)="goLogin()">
              Login
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  `,
  styles: [`
    .page {
      min-height: 100%;
      display: grid;
      place-items: center;
      padding: 24px;
    }

    .auth-box {
      width: 100%;
      max-width: 420px;
    }

    .logo {
      display: grid;
      place-items: center;
      width: 52px;
      height: 52px;
      margin-bottom: 18px;
      border-radius: 16px;
      background: #7089f8;
      color: #fff;
      font-size: 28px;
      font-weight: 800;
    }

    h1 {
      margin: 0;
      font-size: 30px;
      font-weight: 700;
    }

    .subtitle {
      margin: 8px 0 28px;
      color: var(--ion-color-medium);
    }

    ion-input {
      margin: 14px 0;
    }

    .error {
      margin: 12px 0;
      font-size: 14px;
    }

    .main-button {
      margin-top: 22px;
      --border-radius: 12px;
    }

    .footer-link {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2px;
      margin-top: 20px;
      color: var(--ion-color-medium);
      font-size: 14px;
    }

    .footer-link ion-button {
      margin: 0;
      text-transform: none;
      font-weight: 700;
    }
  `],
})
export class RegisterPage {
  private auth = inject(AuthService);
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);

  name = '';
  email = '';
  phone = '';
  password = '';
  confirmation = '';
  loading = false;
  errorMessage = '';

  async submit(): Promise<void> {
    if (this.loading) return;

    if (
      !this.name.trim() ||
      !this.email.trim() ||
      !this.phone.trim() ||
      !this.password ||
      !this.confirmation
    ) {
      this.errorMessage = 'Please complete all fields.';
      return;
    }

    if (this.password.length < 8) {
      this.errorMessage = 'Password must be at least 8 characters.';
      return;
    }

    if (this.password !== this.confirmation) {
      this.errorMessage = 'Passwords do not match.';
      return;
    }

    this.loading = true;
    this.errorMessage = '';

    try {
      await this.auth.register({
        name: this.name.trim(),
        email: this.email.trim(),
        phone: this.phone.trim(),
        password: this.password,
        password_confirmation: this.confirmation,
      });

      await this.router.navigateByUrl('/tabs/tab1', {
        replaceUrl: true,
      });
    } catch (error) {
      if (error instanceof HttpErrorResponse) {
        const errors = error.error?.errors as
          Record<string, string[]> | undefined;

        this.errorMessage =
          error.status === 0
            ? 'Cannot connect to MotoServe API.'
            : (errors ? Object.values(errors)[0]?.[0] : undefined) ??
              error.error?.message ??
              `Registration failed (${error.status}).`;
      } else {
        this.errorMessage =
          error instanceof Error ? error.message : 'Registration failed.';
      }
    } finally {
      this.loading = false;
      this.cdr.markForCheck();
    }
  }

  async goLogin(): Promise<void> {
    await this.router.navigateByUrl('/login');
  }
}