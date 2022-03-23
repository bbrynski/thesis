import { TestBed, inject } from '@angular/core/testing';

import { FormCheckService } from './form-check.service';

describe('FormCheckService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [FormCheckService]
    });
  });

  it('should be created', inject([FormCheckService], (service: FormCheckService) => {
    expect(service).toBeTruthy();
  }));
});
