<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapter extends
    IRequestAdapterMagicAspects,
    IRequestAdapterRequestAspects,
    IRequestAdapterRenderAspects,
    IRequestAdapterSessionAspects,
    IRequestAdapterLogicAspects,
    IRequestAdapterContextAspects {
}
