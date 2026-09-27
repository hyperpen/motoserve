import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import {
  AlertController,
  IonButton,
  IonCard,
  IonCardContent,
  IonCardHeader,
  IonCardTitle,
  IonContent,
  IonHeader,
  IonInput,
  IonModal,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import { CustomerApiService } from '../services/customer-api.service';

interface Motorcycle {
  id: number;
  brand: string;
  model: string;
  year: number | null;
  plate_number: string | null;
  color: string | null;
}

interface MotorcycleForm {
  brand: string;
  model: string;
  year: string | number;
  plate_number: string;
  color: string;
}

interface MotorcycleResponse {
  message: string;
  motorcycle: Motorcycle;
}

@Component({
  selector: 'app-tab2',
  standalone: true,
  imports: [
    FormsModule,
    IonButton,
    IonCard,
    IonCardContent,
    IonCardHeader,
    IonCardTitle,
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
        <ion-title>My Motorcycles</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <main class="ms-shell ms-shell--narrow">
        <div class="ms-intro">
          <div>
            <h1>My Motorcycles</h1>
            <p>Keep your rides ready for service.</p>
          </div>

          <ion-button size="small" (click)="add()">
            + Add Bike
          </ion-button>
        </div>

        @if (successMessage()) {
          <ion-text color="success">
            <p class="ms-feedback">{{ successMessage() }}</p>
          </ion-text>
        }

        @if (errorMessage() && !formOpen()) {
          <ion-text color="danger">
            <p class="ms-feedback">{{ errorMessage() }}</p>
          </ion-text>
        }

        @if (loading()) {
          <p class="ms-muted">Loading motorcycles...</p>
        } @else if (motorcycles().length === 0) {
          <div class="ms-empty">
            <p>No motorcycle saved yet.</p>
            <ion-button size="small" (click)="add()">
              Add your first bike
            </ion-button>
          </div>
        } @else {
          <div class="bike-grid">
            @for (motorcycle of motorcycles(); track motorcycle.id) {
              <ion-card>
                <ion-card-header>
                  <ion-card-title>
                    {{ motorcycle.brand }} {{ motorcycle.model }}
                  </ion-card-title>
                </ion-card-header>

                <ion-card-content>
                  <p>
                    {{ motorcycle.year ?? 'Year not set' }}
                    · {{ motorcycle.color || 'Color not set' }}
                  </p>

                  <p>
                    Plate:
                    {{ motorcycle.plate_number || 'Not specified' }}
                  </p>

                  <div class="ms-actions">
                    <ion-button
                      size="small"
                      fill="outline"
                      (click)="edit(motorcycle)">
                      Edit
                    </ion-button>

                    <ion-button
                      size="small"
                      fill="clear"
                      color="danger"
                      [disabled]="deletingId() !== null"
                      (click)="remove(motorcycle)">
                      {{
                        deletingId() === motorcycle.id
                          ? 'Deleting...'
                          : 'Delete'
                      }}
                    </ion-button>
                  </div>
                </ion-card-content>
              </ion-card>
            }
          </div>
        }
      </main>
    </ion-content>

    <ion-modal
      class="ms-dialog"
      [isOpen]="formOpen()"
      (didDismiss)="closeForm()">
      <ng-template>
        <ion-header>
          <ion-toolbar>
            <ion-title>
              {{
                editingId() === null
                  ? 'Add Motorcycle'
                  : 'Edit Motorcycle'
              }}
            </ion-title>

            <button
              class="ms-close"
              slot="end"
              type="button"
              aria-label="Close"
              (click)="closeForm()">
              ×
            </button>
          </ion-toolbar>
        </ion-header>

        <ion-content class="ion-padding">
          <form class="ms-form" (ngSubmit)="save()">
            <ion-input
              label="Brand"
              labelPlacement="stacked"
              fill="outline"
              name="brand"
              [(ngModel)]="form.brand"
              maxlength="100"
              required>
            </ion-input>

            <ion-input
              label="Model"
              labelPlacement="stacked"
              fill="outline"
              name="model"
              [(ngModel)]="form.model"
              maxlength="100"
              required>
            </ion-input>

            <ion-input
              label="Year (optional)"
              labelPlacement="stacked"
              fill="outline"
              type="number"
              name="year"
              [(ngModel)]="form.year"
              min="1950"
              [max]="currentYear">
            </ion-input>

            <ion-input
              label="Plate Number (optional)"
              labelPlacement="stacked"
              fill="outline"
              name="plate_number"
              [(ngModel)]="form.plate_number"
              maxlength="30">
            </ion-input>

            <ion-input
              label="Color (optional)"
              labelPlacement="stacked"
              fill="outline"
              name="color"
              [(ngModel)]="form.color"
              maxlength="50">
            </ion-input>

            @if (errorMessage()) {
              <ion-text color="danger">
                <p>{{ errorMessage() }}</p>
              </ion-text>
            }

            <div class="ms-form-actions">
              <ion-button
                size="small"
                type="button"
                fill="clear"
                (click)="closeForm()">
                Cancel
              </ion-button>

              <ion-button
                size="small"
                type="submit"
                [disabled]="saving()">
                {{ saving() ? 'Saving...' : 'Save Motorcycle' }}
              </ion-button>
            </div>
          </form>
        </ion-content>
      </ng-template>
    </ion-modal>
  `,
  styles: [`
    ion-input {
      margin: 14px 0;
    }

    .bike-grid {
      display: grid;
      grid-template-columns:
        repeat(auto-fit, minmax(min(100%, 290px), 1fr));
      gap: 12px;
    }

    .bike-grid ion-card {
      margin: 0;
    }
  `],
})
export class Tab2Page implements OnInit {
  private api = inject(CustomerApiService);
  private alertController = inject(AlertController);

  readonly currentYear = new Date().getFullYear();

  motorcycles = signal<Motorcycle[]>([]);
  loading = signal(true);
  saving = signal(false);
  deletingId = signal<number | null>(null);
  editingId = signal<number | null>(null);
  formOpen = signal(false);
  errorMessage = signal('');
  successMessage = signal('');

  form: MotorcycleForm = this.emptyForm();

  ngOnInit(): void {
    void this.loadMotorcycles();
  }

  async loadMotorcycles(): Promise<void> {
    this.loading.set(true);
    this.errorMessage.set('');

    try {
      const response = await firstValueFrom(
        this.api.get<{ motorcycles: Motorcycle[] }>(
          'motorcycles'
        )
      );

      this.motorcycles.set(response.motorcycles);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.loading.set(false);
    }
  }

  async save(): Promise<void> {
    if (this.saving()) return;

    const brand = this.form.brand.trim();
    const model = this.form.model.trim();
    const yearText = String(this.form.year ?? '').trim();

    if (!brand || !model) {
      this.errorMessage.set('Brand and model are required.');
      return;
    }

    const year = yearText === '' ? null : Number(yearText);

    if (
      year !== null &&
      (
        !Number.isInteger(year) ||
        year < 1950 ||
        year > this.currentYear
      )
    ) {
      this.errorMessage.set(
        `Year must be between 1950 and ${this.currentYear}.`
      );
      return;
    }

    const body = {
      brand,
      model,
      year,
      plate_number: this.form.plate_number.trim() || null,
      color: this.form.color.trim() || null,
    };

    this.saving.set(true);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const id = this.editingId();

      const response = id === null
        ? await firstValueFrom(
            this.api.post<MotorcycleResponse>(
              'motorcycles',
              body
            )
          )
        : await firstValueFrom(
            this.api.put<MotorcycleResponse>(
              `motorcycles/${id}`,
              body
            )
          );

      this.closeForm();
      await this.loadMotorcycles();
      this.successMessage.set(response.message);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.saving.set(false);
    }
  }

  add(): void {
    this.cancelEdit();
    this.errorMessage.set('');
    this.formOpen.set(true);
  }

  closeForm(): void {
    this.formOpen.set(false);
    this.cancelEdit();
  }

  edit(motorcycle: Motorcycle): void {
    this.editingId.set(motorcycle.id);

    this.form = {
      brand: motorcycle.brand,
      model: motorcycle.model,
      year: motorcycle.year?.toString() ?? '',
      plate_number: motorcycle.plate_number ?? '',
      color: motorcycle.color ?? '',
    };

    this.errorMessage.set('');
    this.successMessage.set('');
    this.formOpen.set(true);
  }

  cancelEdit(): void {
    this.editingId.set(null);
    this.form = this.emptyForm();
  }

  async remove(motorcycle: Motorcycle): Promise<void> {
    if (this.deletingId() !== null) return;

    const alert = await this.alertController.create({
      cssClass: 'ms-delete-alert',
      header: 'Delete motorcycle?',
      message: 'This motorcycle will be removed from your garage.',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
        },
        {
          text: 'Delete',
          role: 'destructive',
        },
      ],
    });

    await alert.present();

    const { role } = await alert.onDidDismiss();

    if (role !== 'destructive') return;

    this.deletingId.set(motorcycle.id);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const response = await firstValueFrom(
        this.api.delete<{ message: string }>(
          `motorcycles/${motorcycle.id}`
        )
      );

      if (this.editingId() === motorcycle.id) {
        this.closeForm();
      }

      await this.loadMotorcycles();
      this.successMessage.set(response.message);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.deletingId.set(null);
    }
  }

  private emptyForm(): MotorcycleForm {
    return {
      brand: '',
      model: '',
      year: '',
      plate_number: '',
      color: '',
    };
  }

  private describeError(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      if (error.status === 0) {
        return 'Cannot connect to MotoServe API.';
      }

      const errors = error.error?.errors as
        Record<string, string[]> | undefined;

      return (
        (errors ? Object.values(errors)[0]?.[0] : undefined) ??
        error.error?.message ??
        `API error (${error.status}).`
      );
    }

    return error instanceof Error
      ? error.message
      : 'Something went wrong.';
  }
}