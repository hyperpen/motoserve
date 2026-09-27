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
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule, IonButton, IonContent, IonInput, IonText],
  template: `
    <ion-content>
      <div class="page">
        <div class="login-box">
          <div class="logo">M</div>
          <h1>MotoServe</h1>
          <p class="subtitle">Login to your customer account</p>

          <form (ngSubmit)="submit()">
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
              label="Password"
              labelPlacement="stacked"
              fill="outline"
              type="password"
              autocomplete="current-password"
              name="password"
              [(ngModel)]="password"
              required>
            </ion-input>

            @if (errorMessage) {
              <ion-text color="danger">
                <p class="error">{{ errorMessage }}</p>
              </ion-text>
            }

            <ion-button
              class="login-button"
              expand="block"
              type="submit"
              [disabled]="loading">
              {{ loading ? 'Logging in...' : 'Login' }}
            </ion-button>
          </form>

          <div class="register-section">
            <span>Don't have an account?</span>
            <ion-button
              fill="clear"
              size="small"
              type="button"
              [disabled]="loading"
              (click)="goRegister()">
              Register
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

    .login-box {
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

    .login-button {
      margin-top: 22px;
      --border-radius: 12px;
    }

    .register-section {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2px;
      margin-top: 20px;
      color: var(--ion-color-medium);
      font-size: 14px;
    }

    .register-section ion-button {
      margin: 0;
      text-transform: none;
      font-weight: 700;
    }
  `],
})
export class LoginPage {
  private auth = inject(AuthService);
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);

  email = '';
  password = '';
  loading = false;
  errorMessage = '';

  async submit(): Promise<void> {
    if (this.loading) return;

    if (!this.email.trim() || !this.password) {
      this.errorMessage = 'Please enter your email and password.';
      return;
    }

    this.loading = true;
    this.errorMessage = '';

    try {
      await this.auth.login(this.email.trim(), this.password);

      await this.router.navigateByUrl('/tabs/tab1', {
        replaceUrl: true,
      });
    } catch (error) {
      if (error instanceof HttpErrorResponse) {
        this.errorMessage =
          error.status === 0
            ? 'Cannot connect to MotoServe API.'
            : error.error?.errors?.email?.[0] ??
              error.error?.message ??
              `Login failed (${error.status}).`;
      } else {
        this.errorMessage =
          error instanceof Error ? error.message : 'Login failed.';
      }
    } finally {
      this.loading = false;
      this.cdr.markForCheck();
    }
  }

  async goRegister(): Promise<void> {
    await this.router.navigateByUrl('/register');
  }
}