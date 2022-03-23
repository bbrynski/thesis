import { TestBed, inject } from '@angular/core/testing';

import { SkisService } from './skis.service';

describe('SkisService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [SkisService]
    });
  });

  it('should be created', inject([SkisService], (service: SkisService) => {
    expect(service).toBeTruthy();
  }));
});
