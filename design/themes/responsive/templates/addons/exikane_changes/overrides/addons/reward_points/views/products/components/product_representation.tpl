{**
    Override reward points block to hide price in points and add tooltip
**}
<div class="ty-reward-group product-list-field{if !$product.points_info.reward.amount} hidden{/if}">
    <span class="ty-control-group__label">{__("reward_points")}:</span>
    <span class="ty-control-group__item" id="reward_points_{$obj_prefix}{$obj_id}">
        <bdi>{__("points_lowercase", [$product.points_info.reward.amount])}</bdi>
        <a class="cm-tooltip"
           title="{__("reward_points_tooltip") nofilter}"
           style="display: inline-flex;
          align-items: center;
          justify-content: center;
          width: 18px;
          height: 18px;
          border-radius: 50%;
          background: #e8f5e9;
          color: #4caf50;
          font-size: 12px;
          font-weight: bold;
          text-decoration: none;
          border: 1px solid #a5d6a7;
          transition: all 0.2s ease;">?</a>
    </span>
</div>
