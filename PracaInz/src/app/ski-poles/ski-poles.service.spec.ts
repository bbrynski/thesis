import { TestBed, inject } from '@angular/core/testing';

import { SkiPolesService } from './ski-poles.service';

describe('SkiPolesService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [SkiPolesService]
    });
  });

  it('should be created', inject([SkiPolesService], (service: SkiPolesService) => {
    expect(service).toBeTruthy();
  }));
});
